import importlib.util
import stat
import tempfile
import unittest
import zipfile
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]

class ArchivePermissions(unittest.TestCase):
    def test_web_assets_are_readable_after_zip_extraction(self):
        path=ROOT/'scripts/package_update.py'
        self.assertTrue(path.exists(), 'A checked ZIP writer must replace default owner-only writestr permissions')
        spec=importlib.util.spec_from_file_location('package_update',path)
        module=importlib.util.module_from_spec(spec);spec.loader.exec_module(module)
        with tempfile.TemporaryDirectory() as tmp:
            archive=Path(tmp)/'patch.zip'
            with zipfile.ZipFile(archive,'w') as z:
                module.write_member(z,'newton/my_una_styled_posts/js/composer.js',b'// test')
                module.write_member(z,'newton/my_una_styled_posts/template/css/composer.css',b'.test{}')
            with zipfile.ZipFile(archive) as z:
                for item in z.infolist():
                    mode=item.external_attr>>16
                    self.assertEqual(stat.S_IMODE(mode),0o755 if item.is_dir() else 0o644,item.filename)
                    self.assertTrue(mode & stat.S_IROTH,item.filename+' must be readable by Apache')
                self.assertIn('newton/my_una_styled_posts/js/',z.namelist())
                self.assertEqual(z.read('newton/my_una_styled_posts/js/composer.js'),b'// test')

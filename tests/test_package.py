import re
import unittest
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
MODULE = ROOT / 'modules/newton/my_una_styled_posts'

class PackageContract(unittest.TestCase):
    def test_native_identity(self):
        config = (MODULE / 'install/config.php').read_text()
        for value in ["'nw_styled_posts'", "'0.3.0'", "'newton/my_una_styled_posts/'", "'NwStyledPosts'"]:
            self.assertIn(value, config)

    def test_native_files_exist(self):
        for name in ['request.php', 'install/installer.php', 'install/langs/en.xml',
                     'classes/NwStyledPostsModule.php', 'classes/NwStyledPostsConfig.php',
                     'classes/NwStyledPostsDb.php', 'classes/NwStyledPostsTemplate.php',
                     'classes/NwStyledPostsFormEntry.php', 'classes/NwStyledPostsFormsEntryHelper.php',
                     'classes/NwStyledPostsStudioPage.php', 'template/images/icons/std-icon.svg']:
            with self.subTest(name=name):
                self.assertTrue((MODULE/name).is_file(), name)

    def test_sql_statements_survive_una_line_concatenation(self):
        for name in ['install', 'enable', 'disable', 'uninstall']:
            sql = (MODULE/f'install/sql/{name}.sql').read_text()
            for line in sql.splitlines():
                if line.strip() and not line.lstrip().startswith('--'):
                    self.assertTrue(line.rstrip().endswith(';'), f'{name}: {line[:80]}')
            self.assertNotIn('bx_posts', sql)
            self.assertNotIn('boonex/posts', sql)

    def test_registered_class_files_exist(self):
        for sql in (MODULE/'install/sql').glob('*.sql'):
            for relative in re.findall(r"modules/newton/my_una_styled_posts/[A-Za-z0-9_./-]+\.(?:php|svg)", sql.read_text()):
                self.assertTrue((ROOT/relative).is_file(), relative)

    def test_only_module_source_paths(self):
        forbidden = ['inc', 'studio', 'template', 'modules/boonex', 'modules/base']
        for path in forbidden:
            self.assertFalse((ROOT/path).exists(), path)

if __name__ == '__main__':
    unittest.main()

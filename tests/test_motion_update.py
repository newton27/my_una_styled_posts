import re, unittest, xml.etree.ElementTree as ET
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
MODULE=ROOT/'modules/newton/my_una_styled_posts'
UPDATE=ROOT/'updates/update_0.1.0_0.2.0/install'

class MotionUpdate(unittest.TestCase):
 def test_native_update_identity(self):
  config=(UPDATE/'config.php').read_text()
  for text in ["'version_from' => '0.1.0'","'version_to' => '0.2.0'","'compatible_with' => ['15.0.x']","updates/update_0.1.0_0.2.0/","'register_transcoders' => 1"]: self.assertIn(text,config)
  self.assertIn('extends BxDolStudioUpdater',(UPDATE/'installer.php').read_text())
 def test_preserves_data_and_settings(self):
  sql=(UPDATE/'sql/install.sql').read_text()
  self.assertNotRegex(sql,r'(?i)DROP TABLE|TRUNCATE|DELETE FROM `nw_styled_posts_entries`')
  self.assertIn('ADD COLUMN `background_previous`',sql)
  for line in sql.splitlines():
   if line.strip() and not line.startswith('--'): self.assertTrue(line.endswith(';'),line)
 def test_output_is_private_and_cleanup_is_registered(self):
  sql=(MODULE/'install/sql/install.sql').read_text()
  self.assertEqual(len(re.findall(r"'Storage','[^']+','yes',0,0,0,'NwStyledPostsTranscoder'",sql)),2)
  config=(MODULE/'install/config.php').read_text()
  self.assertIn("'unregister_transcoders' => 1",config)
 def test_bundled_loops_have_provenance(self):
  for name in ['tides','glow']:
   self.assertTrue((MODULE/f'template/motion/{name}.mp4').is_file())
   self.assertTrue((MODULE/f'template/motion/{name}.jpg').is_file())
  self.assertIn('MIT',(MODULE/'template/motion/LICENSE.txt').read_text())
 def test_updater_packaging_includes_source(self):
  self.assertTrue((ROOT/'updates/update_0.1.0_0.2.0/install/config.php').is_file())
  self.assertIn('update_0.2.0_0.3.0',(ROOT/'scripts/build.py').read_text())
 def test_native_update_language_uses_add_and_update_tags(self):
  resources=ET.parse(UPDATE/'langs/en.xml').getroot()
  self.assertFalse(resources.findall('string'),'Native updater ignores installation-only string tags')
  additions={s.attrib['name'] for s in resources.findall('string_add')}
  self.assertTrue({'_nw_styled_posts_option_allow_motion_uploads','_nw_styled_posts_option_background_motion_tides','_nw_styled_posts_option_background_motion_glow'}<=additions)

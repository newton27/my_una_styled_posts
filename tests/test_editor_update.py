import re,unittest
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
MODULE=ROOT/'modules/newton/my_una_styled_posts'
class EditorUpdate(unittest.TestCase):
 def test_matching_native_identity(self):
  self.assertIn("'version' => '0.3.1'",(MODULE/'install/config.php').read_text())
  p=ROOT/'updates/update_0.2.0_0.3.0/install/config.php'
  self.assertTrue(p.is_file())
  s=p.read_text()
  for x in ["'version_from' => '0.2.0'","'version_to' => '0.3.0'","'compatible_with' => ['15.0.x']","updates/update_0.2.0_0.3.0/"]:self.assertIn(x,s)
 def test_fields_registered_and_non_destructive(self):
  sql=(ROOT/'updates/update_0.2.0_0.3.0/install/sql/install.sql').read_text()
  full=(MODULE/'install/sql/install.sql').read_text()
  self.assertNotRegex(sql,r'(?i)DROP TABLE|TRUNCATE|DELETE FROM `nw_styled_posts_entries`')
  for key in ['text_backdrop','backdrop_color','backdrop_opacity','backdrop_padding','image_x','image_y','image_zoom','text_direction']:
   self.assertIn('ADD COLUMN `'+key+'`',sql)
   self.assertIn('`'+key+'`',full)
   for display in ['nw_styled_posts_entry_add','nw_styled_posts_entry_edit']:self.assertIn("('"+display+"','"+key+"'",sql)
  for line in sql.splitlines():
   if line.strip() and not line.startswith('--'):self.assertTrue(line.endswith(';'))

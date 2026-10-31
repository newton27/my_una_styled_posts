import unittest,xml.etree.ElementTree as ET
from pathlib import Path
R=Path(__file__).resolve().parents[1]
class PositionUpdate(unittest.TestCase):
 def test_update_is_additive_and_registers_both_forms(self):
  u=R/'updates/update_0.3.3_0.3.4/install';s=(u/'sql/install.sql').read_text()
  self.assertNotRegex(s,r'(?i)DROP|TRUNCATE|DELETE FROM|UPDATE `nw_styled_posts_entries`')
  full=(R/'modules/newton/my_una_styled_posts/install/sql/install.sql').read_text()
  for k in ['text_position','text_x','text_y']:
   self.assertIn('ADD COLUMN `'+k+'`',s);self.assertIn('`'+k+'`',full);self.assertEqual(sum(('`'+k+'`') in line for line in full.splitlines() if line.startswith('CREATE TABLE')),1)
   for f in ['nw_styled_posts_entry_add','nw_styled_posts_entry_edit']:self.assertIn("('"+f+"','"+k+"'",s)
  lang=ET.parse(u/'langs/en.xml').getroot();self.assertEqual(lang.tag,'resources');self.assertEqual(len(lang.findall('string_add')),3)
 def test_native_versions_and_actions(self):
  s=(R/'updates/update_0.3.3_0.3.4/install/config.php').read_text()
  for x in ["'version_from' => '0.3.3'","'version_to' => '0.3.4'","'execute_sql' => 1","'update_files' => 1","'update_languages' => 1","'delete_files' => []"]:self.assertIn(x,s)

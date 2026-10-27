"""Required non-null/no-default columns observed on native UNA 15 RC2 MariaDB."""
import re
import unittest
from pathlib import Path

REQUIRED={
 'sys_form_inputs': {'help','icon'},
 'sys_options': {'check_params'},
 'sys_objects_page': {'content_info','meta_title','inj_head','inj_footer','config_api'},
 'sys_pages_blocks': {'title_system','icon','text','text_updated','help','config_api'},
 'sys_menu_items': {'addon','markers','visibility_custom','config_api'},
 'sys_objects_menu': {'title_public','config_api'},
}
class StrictRegistrations(unittest.TestCase):
 def test_native_required_columns_are_supplied(self):
  root=Path(__file__).resolve().parents[1]/'modules/newton/my_una_styled_posts/install/sql'
  for sql in root.glob('*.sql'):
   for table,columns in re.findall(r'INSERT(?: IGNORE)? INTO `([^`]+)`\s*\(([^)]+)\)',sql.read_text(),re.I):
    if table not in REQUIRED:continue
    provided={c.strip(' `') for c in columns.split(',')}
    with self.subTest(file=sql.name,table=table):
     self.assertFalse(REQUIRED[table]-provided,f'{table}: missing required fields {sorted(REQUIRED[table]-provided)}')

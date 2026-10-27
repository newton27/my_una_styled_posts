import unittest
import xml.etree.ElementTree as ET
from test_package import MODULE

class NativeContracts(unittest.TestCase):
    def test_language_uses_una_resources_root(self):
        self.assertEqual(ET.parse(MODULE/'install/langs/en.xml').getroot().tag,'resources')

    def test_request_is_una_dispatcher_not_an_independent_bootstrap(self):
        source=(MODULE/'request.php').read_text()
        self.assertIn("defined('BX_DOL')",source)
        self.assertIn('BxBaseModTextRequest::processAsAction',source)
        self.assertNotIn('header.inc.php',source)

    def test_entry_schema_has_no_obsolete_attachment_columns(self):
        schema=(MODULE/'install/sql/install.sql').read_text().splitlines()[1]
        for unused in ['`thumb_data`','`multicat`','`abstract`','`location`']:
            self.assertNotIn(unused,schema)

if __name__=='__main__':unittest.main()

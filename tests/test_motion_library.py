import hashlib,json,subprocess,unittest,xml.etree.ElementTree as ET
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
MODULE=ROOT/'modules/newton/my_una_styled_posts'
NAMES=['gradients','waves','clouds','rain','snow','particles','bokeh','northern','ripples','smoke','stars','ribbons']

class MotionLibrary(unittest.TestCase):
 def test_shipped_preview_has_current_motion_library(self):
  html=(ROOT/'preview/index.html').read_text()
  self.assertEqual(html.count('class="nwsp-motion-choice"'),14)
  self.assertIn('STYLED POSTS · 0.3.1',html)
 def test_media_format_and_seam(self):
  import numpy as np
  import importlib.util
  spec=importlib.util.spec_from_file_location('generator',ROOT/'scripts/create_motion_library.py');g=importlib.util.module_from_spec(spec);spec.loader.exec_module(g)
  for name in NAMES:
   with self.subTest(name=name):
    p=MODULE/f'template/motion/{name}.mp4'
    info=json.loads(subprocess.check_output(['ffprobe','-v','error','-show_streams','-show_format','-of','json',str(p)]))
    self.assertEqual(len(info['streams']),1);s=info['streams'][0]
    self.assertEqual((s['codec_name'],s['pix_fmt'],s['width'],s['height']),('h264','yuv420p',640,480))
    self.assertEqual(float(info['format']['duration']),4)
    self.assertLess(p.stat().st_size,8388608)
    self.assertTrue((p.with_suffix('.jpg')).is_file())
    self.assertTrue(np.array_equal(g.frame(name,0),g.frame(name,g.TAU)))
    self.assertGreater(np.abs(g.frame(name,0).astype(float)-g.frame(name,g.TAU/4).astype(float)).mean(),.1)
 def test_native_update_and_settings(self):
  u=ROOT/'updates/update_0.3.0_0.3.1/install'
  cfg=(u/'config.php').read_text()
  self.assertIn("'version_from' => '0.3.0'",cfg);self.assertIn("'version_to' => '0.3.1'",cfg)
  self.assertIn("'version' => '0.3.1'",(MODULE/'install/config.php').read_text())
  xml=ET.parse(u/'langs/en.xml').getroot();self.assertFalse(xml.findall('string'))
  keys={e.attrib['name'] for e in xml.findall('string_add')}
  sql=(u/'sql/install.sql').read_text()
  self.assertNotRegex(sql,r'(?i)DROP|TRUNCATE|DELETE|UPDATE `nw_styled_posts_entries`')
  for name in NAMES:
   key='nw_styled_posts_background_motion_'+name
   self.assertIn("WHERE NOT EXISTS (SELECT 1 FROM `sys_options` WHERE `name` = '"+key+"')",sql)
   self.assertIn('_nw_styled_posts_option_background_motion_'+name,keys)
   self.assertIn(key,(MODULE/'install/sql/enable.sql').read_text())
 def test_old_asset_bytes_unchanged(self):
  expected={'glow.mp4':'36fadc027cc4a02fb35d4eb11dc25e3a652f02ea','tides.mp4':'a39b3ea66b2ebc24de3c25d902c8416f1ecff7d9','glow.jpg':'71f2a2772825453331126a225f7a22101cc8a951','tides.jpg':'8d4fcfbec224afaae5f889a818d9b458e40c6811'}
  for name,sha in expected.items():
   b=(MODULE/'template/motion'/name).read_bytes();self.assertEqual(hashlib.sha1(b'blob '+str(len(b)).encode()+b'\0'+b).hexdigest(),sha)

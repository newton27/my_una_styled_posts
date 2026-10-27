import json, os, re, subprocess, unittest
from test_package import MODULE

def literals(source):
    # MySQL quoted strings: doubled quotes and backslash escapes.
    for m in re.finditer(r"'((?:[^'\\]|\\.|'')*)'",source):
        yield re.sub(r'\\(.)',lambda m:{'n':'\n','r':'\r','t':'\t','0':'\0'}.get(m[1],m[1]),m[1].replace("''", "'"))

class SerializedRegistrations(unittest.TestCase):
    def test_native_service_calls_have_valid_php_serialization(self):
        values=[v for p in (MODULE/'install/sql').glob('*.sql') for v in literals(p.read_text()) if re.match(r'a:\d+:\{',v)]
        self.assertGreater(len(values),5)
        php=os.environ.get('PHP_BINARY','php')
        script='set_error_handler(function($n,$m){throw new Exception($m);}); foreach(json_decode(stream_get_contents(STDIN),true) as $v) {try {unserialize($v,["allowed_classes"=>false]);} catch(Throwable $e) {fwrite(STDERR,$v."\\n".$e->getMessage()."\\n");exit(1);}} echo count(json_decode($argv[1],true));'
        proc=subprocess.run([php,'-n','-r',script,json.dumps(values)],input=json.dumps(values),text=True,capture_output=True)
        self.assertEqual(proc.returncode,0,proc.stderr)

"""Write web-server-readable module ZIP members, including traversable parents."""
import stat
import zipfile
from pathlib import PurePosixPath


def write_member(archive, name, data):
    path=PurePosixPath(name)
    if path.is_absolute() or '..' in path.parts:
        raise ValueError('Archive paths must remain relative')
    for parent in reversed(path.parents):
        if str(parent)=='.':
            continue
        directory=str(parent)+'/'
        if directory not in archive.namelist():
            item=zipfile.ZipInfo(directory)
            item.create_system=3
            item.external_attr=((stat.S_IFDIR|0o755)<<16)|0x10
            archive.writestr(item,b'')
    item=zipfile.ZipInfo(name)
    item.create_system=3
    item.external_attr=(stat.S_IFREG|0o644)<<16
    item.compress_type=zipfile.ZIP_DEFLATED
    archive.writestr(item,data)

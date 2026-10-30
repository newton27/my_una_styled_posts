# Styled Posts 0.3.3 comments heading correction

The previous adapter inserted getCounter() HTML into Comments ({0}), splitting the heading around a block widget. Use getCommentsCountAll(0, true), matching UNA's native heading implementation.

Version 0.3.3 is based on motion-backgrounds checkpoint d25e19ad0c5b645994ad5765e7ba0c75487306ef. Adds the native 0.3.2-to-0.3.3 updater. Older sites must complete their existing version-matched update chain first.

Regression coverage: zero, one, and twelve comments; title requests a recalculated total and contains no widget markup. Existing native item-template coverage retained.

Validation pending: PHP execution, native Studio clean install and 0.3.2-to-0.3.3 update, browser heading and post-comment refresh. No production deployment or Marketplace publication has been performed. Do not distribute as a certified release until these gates pass.

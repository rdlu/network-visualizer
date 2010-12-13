<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="pt-BR">
    <head profile="http://gmpg.org/xfn/11">
        <title><?php echo $title ?></title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Language" content="pt-BR" />
        <link href="img/favicon.png" rel="favicon" type="image/png" />
        <?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), "\n" ?>
        <?php foreach ($scripts as $file) echo HTML::script($file), "\n" ?>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <?php echo $header ?>
            </div>
            <div id="content">
                <?php echo $content ?>
            </div>
            <div id="footer">
                <?php echo $footer ?>
            </div>
        </div>
        <?php if(isset($extras)) echo $extras; ?>
    </body>
</html>
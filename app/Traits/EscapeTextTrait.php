<?php namespace App\Traits;

trait EscapeTextTrait
{

    //!Hola adrian prueba

    private $findWords = [
        '<?','?>','&lt;script&gt;', '&lt;/script&gt;', '&lt;SCRIPT&gt;', '&lt;/SCRIPT&gt;','<?php','<template','&lt;template','</template','&lt;/template',
        '?>','alert(','<scrip','t>','</scrip', '<SCRIP','T>', '</SCRIP', '&lt;script&gt;', '&lt;/script&gt;','</meta','<META','</META','&lt;meta&gt;',
        '&lt;/meta&gt;','meta&gt;','animatetransform ','/sup','/svg','innerHTML','/svg-xml','xml:','ActiveXObject','injected','@import',
        '?&gt;','script&gt;','?php', '&lt;SCRIPT&gt;', '&lt;/SCRIPT&gt;', 'qavalue', '1=1', '1 = 1', 'script','HACKTEMP',
        '*/', '/*', 'OR 1', 'or 1', "OR '1", "or '1", "OR -", 'href=', '--', '&lt;a', '&lt;/a&gt;', '&lt;A','confirm(',
        '&lt;/A&gt;', 'xss', 'XSS', '<svg','&lt;svg','&#97&#108&#101&#114&#116','onload=','onload','xmlns:',
        '<iframe','&lt;iframe','prompt(', '<marquee','<MARQUEE','&lt;marquee','</marquee','</MARQUEE','&lt;/marquee'.
        'data:text/html;base64','data:',':text/',':text','/html','base64','onrepeat','onend=','animateMotion','repeatCount=',
        'onerror=','onmouseover','document.domain','java:','onerror','onfocus','onblur','oncontextmenu','oncut','ondblclick','ondrag',
        'contenteditable','onkeypress','onmousedown','onmousemove','brutelogic','setInterval','appendChild','HOST:PORT','createElement',
        'formaction=','xlink:','xlink=','ontouchstart','onpageshow','onscroll','onresize','onhelp','onstart','top[','eval(','onkeydown','onmouseleave',
        'onmouseup','ondragleave','onbeforeactivate','autofocus','onbeforecopy','ondragend','draggable=','rticle',':[','getElementById','dataformatas',
        'EVENT=','match=','xmlns','x-schema','?xml','x:','x=','j$','handler=','ev:','foreiHOST:PORTgnObject','/svg','foo=','feImage','mhtml:','x:x','clip-path:',
        '.svg','innerHTML','text/plain','dataTransfer','event.','view-source:','makePopups','window.open','text/xml','xml-stylesheet','xsl:', 'start=',
        'ATTLIST','DOCTYPE','version=','/handler','feed:','tab/traffic','attributeName=','begin=','oncopy=','onclick=','<brute','&lt;brute','id=',
        'ontouchend=','onorientationchange=','location.','slice(','onhashchange=','onfinish=','onsubmit=','onbeforedeactivate=','onmouseenter=',
        'ondeactivate=','onmouseout=','onbeforepaste=','onkeyup=','/acronym','mocha:','@keyframes','onbeforecut=','HTTP-EQUIV=','onactivate','onpaste',

        '\u','\x','%3c%3f','%26lt%3b','%26gt%3b','alert%28','%3cscrip','%3c%2fmeta','%3cMETA','%5c%3e','slice%28','j%24','%3f%3e','%40import','%2fsup',
        '%2fsvg','%2fsvg-xml','xml%3a','%3f%26gt','href%3d','confirm%28','onload%3d','xmlns%3a','%3ciframe','%26lt%3biframe','prompt%28','java%3a','data%3a',
        '%3cmarquee','%26lt%3bmarquee','%3c%2fmarquee','data%3atext%2fhtml%3bbase64','%3atext%2f','%2fhtml','repeatCount%3d','formaction%3d','xlink%3a','top%5b',
        'eval%28','%2fsvg','%3ctemplate','%26lt%3btemplate','%3a%5b','x%3ax','clip-path%3a','text%2fplain','%3fxml','x%3a','x%3d','handler%3d','ev%3a','foo%3d',
        'view-source%3a','start%3d','version%3d','text%252fplain','tab%2ftraffic','attributeName%3d','begin%3d','oncopy%3d','%3cbrute','%26lt%3bbrute','id%3d',
        'ontouchend%3d','onmouseout%3d','onbeforepaste%3d','onkeyup%3d','onbeforecut%3d','onactivate%3d','onpaste%3d','%2facronym','mocha%3a','%40keyframes',
        'HTTP-EQUIV%3d','onmouseenter%3d','&#','%3c%2f','%2f%3e','%3c','%3e','onwheel%3d','onwheel','Function%28','Function(','%3cparam','<param','&lt;param',
        'cell%28', 'cell(','WEBSERVICE(','WEBSERVICE%28','atob(','atob%28'
    ];

    public function escapeText($text)
    {
        $text = $this->disableExcelFunctions($text);

        foreach ($this->findWords as $word) {
            // Crear un patrón de expresión regular que busque la palabra exacta
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $text = preg_replace($pattern, '', $text);
        }

        // Eliminar espacios adicionales
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);

    }

    public function disableExcelFunctions($text)
    {
        $text = trim($text);

        if ($text !== '' && ($text[0] === "=" || $text[0] === "+"|| $text[0] === "-" || $text[0] === "@")){
            $text = substr($text, 1);
        }

        return trim($text);
    }
}

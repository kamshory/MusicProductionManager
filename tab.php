<?php

use PhpTabs\PhpTabs;

require_once "inc/app.php";


$tab = new PhpTabs('Lagu 0014.mid');

// Available options

/**
 * options scale=0.8 space=16 width=500 tab-stems=true tab-stem-direction=down stave-distance=20 font-size=12 font-face=times font-style=italic tempo=66

tabstave notation=true time=4/4
 */
$options = [
  // Renderer options
  'measures_per_stave'  => 1,

  // Global options
  'space'               => 16,        # An integer
  'scale'               => 0.8,       # A float or an integer
  'stave-distance'      => 20,        # An integer
  'width'               => 500,       # An integer, in pixel

  'font-size'           => 12,        # An integer
  'font-face'           => 'times',   # A string
  'font-style'          => 'italic',  # A string

  'tab-stems'           => true,      # A boolean, default: false
  'tab-stem-direction'  => 'down',    # A string up|down, default: up
  'player'              => false,     # A boolean, default: false

  // Tabstaves options
  'notation'            => true,       # A boolean, default: false
  'tablature'           => false,       # A boolean, default: true
  'lyric'               => true,
];

//tab-stems=true tab-stem-direction=down


// Render track 0
$tabStr = $tab
  ->getRenderer('vextab')
  ->setOptions($options)
  ->render(3);

$arr = explode("\n", $tabStr);
foreach($arr as $key=>$val)
{
  if(trim($val) == 'notes')
  {
    $arr[$key] = '';
  }
}
$tabStr = implode("\n", $arr);


  ?><!DOCTYPE html>
  <html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
  
    <script
      type="text/javascript"
      src="fiddle.jshell.net_files/dummy.js"
      
    ></script>
  
      <link rel="stylesheet" type="text/css" href="fiddle.jshell.net_files/result-light.css">
  
      <script type="text/javascript" src="https://unpkg.com/vextab/releases/main.dev.js"></script>
    <style id="compiled-css" type="text/css">
      .vexbox {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
  }
  
      /* EOS */
    </style>
  
    <script id="insert"></script>
  
      <script src="fiddle.jshell.net_files/stringify.js?ceaeb44bfd207968b44d260d6b94e6686e85ba92" charset="utf-8"></script>
      
  </head>
  <body>
      <div class="vexbox">
    <div id="boo"></div>
  </div>
  
  <script type="text/javascript">//<![CDATA[

let data2 = `
<?php echo $tabStr;?>
`;
let data = `
options tab-stems=true tab-stem-direction=down
tabstave notation=true tablature=true time=4/4
notes :8 3/5 0-2-3/4 0-2/3 0-1-1-0/2 2-0/3 3-2-0/4 3/5
text :8,.1,C,D,E,F,G,A,B,C,C,B,A,G,F,E,D,C
`

const VF = vextab.Vex.Flow

const renderer = new VF.Renderer($('#boo')[0],
	VF.Renderer.Backends.SVG);
  
  vextab.Artist.NOLOGO = true;

// Initialize VexTab artist and parser.
const artist = new vextab.Artist(10, 10, 750, { scale: 0.8 });
const tab = new vextab.VexTab(artist);

tab.parse(data2);
artist.render(renderer);


  //]]></script>

  <script>
    // tell the embed parent frame the height of the content
    if (window.parent && window.parent.parent){
      window.parent.parent.postMessage(["resultsFrame", {
        height: document.body.getBoundingClientRect().height,
        slug: "umkfjxyv"
      }], "*")
    }

    // always overwrite window.name, in case users try to set it manually
    window.name = "result"
  </script>

    <script>
      let allLines = []

      window.addEventListener("message", (message) => {
        if (message.data.console){
          let insert = document.querySelector("#insert")
          allLines.push(message.data.console.payload)
          insert.innerHTML = allLines.join(";\r")

          let result = eval.call(null, message.data.console.payload)
          if (result !== undefined){
            console.log(result)
          }
        }
      })
    </script>
  
  </body>
  </html>
  
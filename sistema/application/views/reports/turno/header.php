<?php $color = "rgb(52,194,159)"; ?>
<link rel="stylesheet" type="text/css" href="/sistema/resources/fonts/lato/lato.css" />
<style type="text/css">
#header_pymvar button {
    background-color: <?php echo $color ?>;
    border: none;
    padding: 5px 10px;
    line-height: 1.5;
    font-size: 12px;
    display: inline-block;
    cursor: pointer;
    color: white;
    vertical-align: middle;
    -webkit-box-shadow: 0 1px 1px rgba(90,90,90,0.1);
    box-shadow: 0 1px 1px rgba(90,90,90,0.1);
    border-radius: 2px;
    outline: none;
    font-weight: 500;
}
#header_pymvar button:hover, #header_pymvar button:focus {
    background-color: rgb(12,154,119);
}
#header_pymvar button.btn-negro {
    background-color: #282f38;
}
@media screen {
    #header_pymvar { z-index: 9999; overflow: hidden; position: fixed; top: 0px; width: 100%; left: 0px; height: 50px; background-color: white; border-bottom:solid 2px #CCC; }
    #header_pymvar .centrado { width: 210mm; margin: 0 auto; }
    .header_logo { color: <?php echo $color ?>; width: 300px; height: 80%; display: block; float: left; margin-top: 5px; }
    .header_logo .header_logo_imagen { float: left; display: block; width: 43px; height: 37px; background: url(http://www.varcreative.com/templates/proyectos/images/logo2.png) no-repeat 50% 50% <?php echo $color ?>; background-size: cover; }
    .header_logo .header_logo_nombre { font-family: 'LatoRegular'; text-transform: uppercase; float: left; margin-left: 5px; line-height: 37px; font-size: 22px; font-weight: bold; }
    .header_logo .header_logo_nombre .negro { color:#282f38; }
    body { padding-top: 60px; }
    .header_pymvar_botones { margin-top: 10px; float: right; }
}
@media print {
    #header_pymvar { display: none; }
    body { padding: 0px; }    
}
</style>
<script type="text/javascript" src="/sistema/resources/js/jquery.1.11.0.min.js"></script>
<script type="text/javascript" src="/sistema/resources/js/jspdf.min.js"></script>
<script type="text/javascript" src="/sistema/resources/js/html2canvas.min.js"></script>
<script type="text/javascript">
function imprimir() {
    window.print();
}
function createPDF(){
    // Comenzamos a renderizar todas las paginas
    for(var i=0;i<$(".a4").length;i++) {
        getCanvas(i);
    }
    setInterval(checkRender,100);
}

function compare(a,b){
    if (a.numero < b.numero) return -1;
    else if (a.numero > b.numero) return 1;
    else return 0;
}

// En este array se van guardanlo los canvas a medidas que se van renderizando
var paginas = new Array();
function checkRender() {
    // Si se completo el proceso
    if (paginas.length == $(".a4").length) {
        
        // Ordenamos el array
        paginas.sort(compare);
        
        var doc = new jsPDF({
            unit:'px', 
            format:'a4'
        });        
        for(var i=0;i<paginas.length;i++) {
            var pagina = paginas[i];
            var img = pagina.canvas.toDataURL("image/jpeg", 1.0);
            doc.addImage(img, 'JPEG', 0, 0);
            if (i != paginas.length-1) doc.addPage();
        }
        var titulo = $(document).find("title").text();
        doc.save(titulo+'.pdf');
        paginas = new Array();
    }
}

function getCanvas(page){
    return html2canvas($('.a4:eq('+page+')'),{
        imageTimeout:2000,
        removeContainer:true,
        onrendered:function(canvas){
            // Agregamos el canvas en el array
            window.paginas.push({
                "numero": page,
                "canvas": canvas
            });
        }
    }); 
}    
</script>
<div id="header_pymvar">
    <div class="centrado">
        <a class="header_logo" href="http://www.shopvar.com/" target="_blank">
            <div class="header_logo_imagen"></div>
            <span class="header_logo_nombre"><span class="negro">Shop</span>var</span>
        </a>
        <div class="header_pymvar_botones">
            <button class="btn btn-negro" onclick="imprimir()">Imprimir</button>
        </div>
    </div>
</div>
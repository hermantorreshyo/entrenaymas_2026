<style type="text/css">
@media screen {
    #header_pymvar { z-index: 9999; overflow: hidden; position: fixed; top: 0px; width: 100%; left: 0px; height: 50px; background-color: white; border-bottom:solid 2px #CCC; }
    #header_pymvar .centrado { width: 210mm; margin: 0 auto; }
    @media (max-width: 768px) {
      #header_pymvar .centrado { width: 100%; margin: 0 auto; }
    }
    #header_pymvar_logo { height: 100%; display: block; float: left; }
    #header_pymvar_logo img { height: 40px; margin-top: 5px; }
    body { padding-top: 60px; }
    .header_pymvar_botones { margin-top: 10px; float: right; }
    .btn-default { 
      background-color: #0dbba9;
      border: none;
      color: white;
      padding: 5px 15px;
      border-radius: 5px;
      text-shadow: 1px 1px 1px #969696;
      cursor: pointer; 
    }
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
  setTimeout(function(){
    // Comenzamos a renderizar todas las paginas
    for(var i=0;i<$(".a4").length;i++) {
      getCanvas(i);
    }
    checkRender();
  },1000);
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
  html2canvas($('.a4:eq('+page+')')[0],{
    async:false,
    imageTimeout:0,
    removeContainer:true,
  }).then(function(canvas){
    // Agregamos el canvas en el array
    window.paginas.push({
      "numero": page,
      "canvas": canvas
    });
  }); 
}    
</script>
<div id="header_pymvar">
    <div class="centrado">
        <a id="header_pymvar_logo" href="https://www.shopvar.com/" target="_blank">
          <img src="https://www.varcreative.com/sistema/resources/images/shopvar-logo-full.png"/>
        </a>
        <div class="header_pymvar_botones">
            <button class="btn btn-default" onclick="imprimir()">Imprimir</button>
            <!--<button class="btn btn-primary" onclick="createPDF()">Descargar PDF</button>-->
        </div>
    </div>
</div>
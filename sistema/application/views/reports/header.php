<style type="text/css">
#header_pymvar button {
    background-color: #ac1c30;
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
    #header_pymvar_logo { height: 100%; display: block; float: left; }
    #header_pymvar_logo img { height: 80%; margin-top: 6px; }
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
</script>
<div id="header_pymvar">
    <div class="centrado">
        <a id="header_pymvar_logo" href="https://www.shopvar.com/" target="_blank">
            <img src="/sistema/resources/images/shopvar-logo-full.png"/>
        </a>
        <div class="header_pymvar_botones">
            <button class="btn btn-default" onclick="imprimir()">Imprimir</button>
        </div>
    </div>
</div>
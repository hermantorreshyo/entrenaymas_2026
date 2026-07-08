<style type="text/css">
.printer-title-cont { display: none; }
@media print {
  .page-break { page-break-after: always !important; }
  .panel { border: solid 1px #f6f8f8 !important; height: auto !important; }
  .panel.bg-success { background-color: #48cfae !important; color: #c2f3ce !important; border: none !important; }
  .panel.bg-success .text-white { color: white !important; }
  .panel.bg-info { background-color: #4dbecf !important; color: #d9f3fb !important; border: none !important; }
  .panel.bg-info .text-white { color: white !important; }
  .panel.bg-success .text-muted { color: #98eaab !important; }
  .panel.bg-info .text-muted { color: #ace4f5 !important; }
  .panel-default .panel-heading { background-color: #f6f8f8 !important; }
  .panel-default .panel-heading.font-bold { font-weight: normal !important; }
  .bg-light.lter, .bg-light .lter { background-color: transparent !important; }
  .no-print { display: none; }
  .printer-title-cont { display: block; overflow: hidden; margin-bottom: 30px; border-bottom: solid 2px #eee; padding-bottom: 20px; }
  .print-logo { float: left; clear: both; }
  .print-logo img { max-width: 400px; max-height: 120px; margin-bottom: 20px; }
  .google-analytics { margin-bottom: 15px; text-align: right; overflow: hidden; }
  .google-analytics img { width: 160px; display: block; float: right; }
  .printer-subtitle { margin: 10px 0px; color: #222; font-size: 16px; }
}
</style>
<div class="printer-title-cont">
  <% var logo_reporte = (!isEmpty(LOGO)) ? LOGO : ((!isEmpty(LOGO_1))?LOGO_1:"") %>
  <% if (!isEmpty(logo_reporte)) { %>
    <div class="print-logo">
      <img src="/sistema/<%= logo_reporte %>"/>
    </div>
  <% } %>
  <div class="fr tar">
    <div class="google-analytics">
      <img src="/sistema/resources/images/ga.png"/>
    </div>
    <h1 class="m-n font-thin h3 text-black"><i class="glyphicon glyphicon-stats icon icono_principal"></i><%= (IDIOMA=="en")?"Statistics":"Estadisticas" %>
      / <b class="printer-title"></b>
    </h1>
    <p class="printer-subtitle"></p>
  </div>
</div>
<style type="text/css">
.propiedad_preview .modal-body { background-color: #f0f3f4 !important; }
.propiedad_preview .modal-body .titulo { font-weight: bold; font-size: 22px; color: #2e3345; }
.propiedad_preview .modal-body .expand { background-color: #f5f6f7; color: #393e50; font-size: 15px; }
.propiedad_preview .modal-body .expand .subtitulo { font-size: 18px; font-weight: bold; padding: 10px 0px; border-bottom: solid 1px #dddedf; margin-bottom: 15px; color: #2e3345; }
#propiedades_preview_slider { margin: 0px 0px 10px 0px; border: none; }
.propiedad-preview .flex-direction-nav a { text-shadow: none; width: 46px; height: 46px; background-color: white; border-radius: 100%; text-align: center; }
.propiedad-preview .flex-direction-nav a:before { font-size: 20px; line-height: 50px; }
.propiedad-preview .flex-direction-nav a.flex-prev:before { text-indent: -3px; }
.propiedad-preview .flex-direction-nav a.flex-next:before { text-indent: 3px; }
#propiedades_preview_carousel { margin: 0px; border: none; }
#propiedades_preview_carousel .slides img { padding: 5px; cursor: pointer; }
</style>
<div class='modal-content propiedad-preview'>
  <div class='modal-body'>
    <div class="row pl10 pr10">
      <div class="col-md-6 pl5 pr5">
        <div class="panel panel-default">
          <div class="tab-container mb0">
            <ul class="nav nav-tabs nav-tabs-2" role="tablist">
              <li class="active">
                <a id="propiedad_preview_1_link" href="#propiedad_preview_tab1" class="buscar_todos" role="tab" data-toggle="tab">
                  <i class="fa text-success fa-camera m-r-xs"></i>
                  Im&aacute;genes
                </a>
              </li>
              <li>
                <a id="propiedad_preview_2_link" href="#propiedad_preview_tab2" role="tab" data-toggle="tab">
                  <i class="fa text-danger fa-map-marker m-r-xs"></i>
                  Ubicaci&oacute;n
                </a>
              </li>
              <% if(!isEmpty(nota_publica) || !isEmpty(nota_privada)) { %>
                <li>
                  <a id="propiedad_preview_4_link" href="#propiedad_preview_tab4" role="tab" data-toggle="tab">
                    <i class="fa text-primary fa-file-text m-r-xs"></i>
                    Notas
                  </a>
                </li>
              <% } %>
              <% if (id_empresa == ID_EMPRESA && id_propietario != 0) { %>
                <li>
                  <a id="propiedad_preview_5_link" href="#propiedad_preview_tab5" role="tab" data-toggle="tab">
                    <i class="fa text-info fa-user m-r-xs"></i>
                    Propietario
                  </a>
                </li>
              <% } %>

              <% if (id_empresa != ID_EMPRESA) { %>
                <li>
                  <a id="propiedad_preview_3_link" href="#propiedad_preview_tab3" role="tab" data-toggle="tab">
                    <i class="fa text-info fa-globe m-r-xs"></i>
                    Datos de la Red
                  </a>
                </li>
              <% } %>
            </ul>
            <div class="tab-content">

              <div id="propiedad_preview_tab1" class="tab-pane active">
                <div id="propiedades_preview_slider" class="flexslider">
                  <ul class="slides">
                    <% var imagen = (isEmpty(path)) ? "" : ((path.indexOf("http")==0) ? path : '/sistema/'+path) %>
                    <li>
                      <div style="overflow: hidden; width: 100%; height: 400px; background-image: url(<%= imagen %>); background-repeat: no-repeat; background-position: center center; background-size: contain"></div>
                    </li>
                    <% for (var i=0;i< images.length;i++) { %>
                      <% var im2 = images[i] %>
                      <% var im = (isEmpty(im2)) ? "" : ((im2.indexOf("http")==0) ? im2 : '/sistema/'+im2) %>
                      <li>
                        <div style="overflow: hidden; width: 100%; height: 400px; background-image: url(<%= im %>); background-repeat: no-repeat; background-position: center center; background-size: contain"></div>
                      </li>
                    <% } %>
                  </ul>
                </div>
                <div id="propiedades_preview_carousel" class="flexslider">
                  <ul class="slides">
                    <% var imagen = (isEmpty(path)) ? "" : ((path.indexOf("http")==0) ? path : '/sistema/'+path) %>
                    <li>
                      <div style="overflow: hidden; width: 100%; height: 100px; background-image: url(<%= imagen %>); background-repeat: no-repeat; background-position: center center; background-size: contain"></div>
                    </li>
                    <% for (var i=0;i< images.length;i++) { %>
                      <% var im2 = images[i] %>
                      <% var im = (isEmpty(im2)) ? "" : ((im2.indexOf("http")==0) ? im2 : '/sistema/'+im2) %>
                      <li>
                        <div style="overflow: hidden; width: 100%; height: 100px; background-image: url(<%= im %>); background-repeat: no-repeat; background-position: center center; background-size: contain"></div>
                      </li>
                    <% } %>
                  </ul>
                </div>
              </div>

              <div id="propiedad_preview_tab2" class="tab-pane">
                <div style="height:510px;" id="propiedad_preview_mapa"></div>
              </div>

              <div id="propiedad_preview_tab4" class="tab-pane">
                <% if (id_empresa == ID_EMPRESA && !isEmpty(nota_privada)) { %>
                  <div class="mb30">
                    <b>Nota Privada:</b><br/>
                    <%= nota_privada %>
                  </div>
                  <hr/>
                <% } %>
                <% if(!isEmpty(nota_publica)) { %>
                  <div>
                    <b>Nota P&uacute;blica:</b><br/>
                    <%= nota_publica %>
                  </div>
                <% } %>
              </div>

              <% if (id_empresa == ID_EMPRESA) { %>
                <div id="propiedad_preview_tab5" class="tab-pane">
                  <div class="mb30">
                    <p class="bold">Datos del Propietario:</p>
                    <p>
                      <a class="cp" href="app/#cliente/<%= id_propietario %>" target="_blank"><%= propietario %></a>
                    </p>
                    <% if (!isEmpty(propietario_telefono)) { %>
                      <p>
                        <span class="dib w20 tar m-r-sm"><i class="fa text-info fa-phone"></i></span> <a href="javascript:void(0)"><%= propietario_telefono %></a>
                      </p>
                    <% } %>
                    <% if (!isEmpty(propietario_celular)) { %>
                      <p>
                        <span class="dib w20 tar m-r-sm"><i class="fa text-info fa-phone"></i></span> <a href="javascript:void(0)"><%= propietario_celular %></a>
                      </p>
                    <% } %>
                    <% if (!isEmpty(propietario_email)) { %>
                      <p>
                        <span class="dib w20 tar m-r-sm"><i class="fa text-info fa-envelope"></i></span> <a href="mailto:<%= propietario_email %>"><%= propietario_email %></a>
                      </p>
                    <% } %>
                    <% if (!isEmpty(propietario_direccion)) { %>
                      <p>
                        <span class="dib w20 tar m-r-sm"><i class="fa text-info fa-home"></i></span> <a href="javascript:void(0)"><%= propietario_direccion %></a>
                      </p>
                    <% } %>
                  </div>
                </div>
              <% } %>

              <div id="propiedad_preview_tab3" class="tab-pane">
                <div class="titulo mt5 mb10"><%= empresa %></div>
                <% if (!isEmpty(empresa_direccion)) { %>
                  <div class="clearfix mb15">
                    <b>Direcci&oacute;n:</b> <span><%= empresa_direccion %></span>
                  </div>
                <% } %>
                <% if (!isEmpty(empresa_telefono)) { %>
                  <div class="clearfix mb15">
                    <b>Tel&eacute;fono:</b> <span><%= empresa_telefono %></span>
                  </div>
                <% } %>
                <% if (!isEmpty(empresa_email)) { %>
                  <div class="clearfix mb15">
                    <b>Email:</b> <a class="text-info cp" href="mailto:<%= empresa_email %>"><%= empresa_email %></a>
                  </div>
                <% } %>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 pl5 pr5">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <div class="clearfix">
                <% if (!isEmpty(codigo)) { %>
                  <b>C&oacute;digo:</b> <span><%= codigo %></span> | 
                <% } %>
                <span><%= tipo_inmueble %></span>

                <% if (id_tipo_estado == 1) { %>
                  <span class="label fs14 pull-right bg-success"><%= tipo_estado %></span>
                <% } else if (id_tipo_estado == 2) { %>
                  <span class="label fs14 pull-right bg-warning"><%= tipo_estado %></span>
                <% } else if (id_tipo_estado == 3) { %>
                  <span class="label fs14 pull-right bg-danger"><%= tipo_estado %></span>
                <% } else if (id_tipo_estado == 4) { %>
                  <span class="label fs14 pull-right bg-primary"><%= tipo_estado %></span>
                <% } else if (id_tipo_estado == 5) { %>
                  <span class="label fs14 pull-right bg-info"><%= tipo_estado %></span>
                <% } %>
              </div>

              <div class="titulo mt5 mb10"><%= nombre %></div>

              <div class="clearfix mb15">
                <% if (!isEmpty(calle)) { %>
                  <b>Direcci&oacute;n:</b> <span><%= calle %> <%= altura %> <%= (!isEmpty(piso)) ? "Piso: "+piso:"" %> <%= (!isEmpty(numero)) ? "Dpto: "+numero:"" %></span> | 
                <% } %>
                <span><%= localidad %></span>
              </div>

              <div class="clearfix">
                <% if (id_tipo_operacion == 1) { %>
                  <span class="label fs14 pull-left mt5 bg-danger"><%= tipo_operacion %></span>
                <% } else if (id_tipo_operacion == 2) { %>
                  <span class="label fs14 pull-left mt5 bg-info"><%= tipo_operacion %></span>
                <% } else if (id_tipo_operacion == 3) { %>
                  <span class="label fs14 pull-left mt5 bg-primary"><%= tipo_operacion %></span>
                <% } else if (id_tipo_operacion == 4) { %>
                  <span class="label fs14 pull-left mt5 bg-success"><%= tipo_operacion %></span>
                <% } else if (id_tipo_operacion == 5) { %>
                  <span class="label fs14 pull-left mt5 bg-warning"><%= tipo_operacion %></span>
                <% } %>

                <div class="titulo ml10 pull-left"><%= moneda %> <%= Number(precio_final).format(0) %></div>

                <% if (apto_banco==1) { %>
                  <span class="text-info pull-right fs14 mt5"><img class="pr t-3" src="/sistema/resources/images/banco.png"/> <span class="mt5">APTO CR&Eacute;DITO BANCARIO</span></span>
                <% } %>
                <% if (acepta_permuta==1) { %>
                  <span class="text-info pull-right fs14 mt5"><img class="pr t-3" src="/sistema/resources/images/banco.png"/> <span class="mt5">ACEPTA PERMUTA</span></span>
                <% } %>

              </div>

            </div>
          </div>
          <div class="panel-body expand db">
            <div class="padder">
              <div class="subtitulo">Informaci&oacute;n B&aacute;sica</div>
              <div><%= texto %></div>
              <div class="subtitulo">Caracter&iacute;sticas</div>
              <div class="row pl10 pr10">
                <div class="col-sm-4 col-xs-6 mb15 pl5 pr5">
                  <b>Dormitorios: </b> <%= (dormitorios>0)?dormitorios:"-" %>
                </div>
                <div class="col-sm-4 col-xs-6 mb15 pl5 pr5">
                  <b>Ba&ntilde;os: </b> <%= (banios>0)?banios:"-" %>
                </div>
                <div class="col-sm-4 col-xs-6 mb15 pl5 pr5">
                  <b>Cochera: </b> <%= (cocheras>0)?cocheras:"No posee" %>
                </div>
                <div class="col-sm-4 col-xs-6 mb15 pl5 pr5">
                  <b>Sup. Total: </b> <%= (superficie_total>0)?superficie_total+" Mts.<sup>2</sup>":"-" %>
                </div>
                <div class="col-sm-4 col-xs-6 mb15 pl5 pr5">
                  <b>Antig&uuml;edad: </b> 
                  <%= (nuevo == 0)?"No definida":"" %>
                  <%= (nuevo == 1)?"A estrenar":"" %>
                  <%= (nuevo == 2)?"Aprox. 2 a&ntilde;os":"" %>
                  <%= (nuevo == 5)?"Aprox. 5 a&ntilde;os":"" %>
                  <%= (nuevo == 10)?"Aprox. 10 a&ntilde;os":"" %>
                  <%= (nuevo == 20)?"Aprox. 20 a&ntilde;os":"" %>
                  <%= (nuevo == 30)?"Aprox. 30 a&ntilde;os":"" %>
                  <%= (nuevo == 40)?"Aprox. 40 a&ntilde;os":"" %>
                  <%= (nuevo == 50)?"Aprox. 50 a&ntilde;os":"" %>
                  <%= (nuevo == 60)?"Aprox. 60 a&ntilde;os":"" %>
                  <%= (nuevo == 70)?"Aprox. 70 a&ntilde;os":"" %>
                  <%= (nuevo == 80)?"Aprox. 80 a&ntilde;os":"" %>
                  <%= (nuevo == 90)?"Aprox. 90 a&ntilde;os":"" %>
                  <%= (nuevo == 100)?"Aprox. 100 a&ntilde;os":"" %>
                  <%= (nuevo == 200)?"M&aacute;s de 100 a&ntilde;os":"" %>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-footer">
          <div>
            <button class="btn btn-default mr5 enviar_whatsapp btn-addon"><i class="icon text-success fa fa-whatsapp"></i>Enviar Whatsapp</button>
            <button class="btn btn-default mr5 marcar_interes btn-addon"><i class="icon text-warning fa fa-star"></i>Marcar Inter&eacute;s</button>
            <button class="btn btn-default mr5 enviar btn-addon"><i class="icon fa text-info fa-send"></i>Enviar Email</button>
            <% if (id_empresa == ID_EMPRESA) { %>
              <button class="btn btn-default mr5 editar"><i class="icon fa fa-pencil"></i></button>
            <% } %>
          </div>
        </div>


      </div>
    </div>
  </div>
</div>

# Entrena y Más — Documento de Requerimientos para Reconstrucción

> Generado a partir del análisis del código fuente actual (`entrenaymas_2026`), que corre sobre una plataforma
> white-label multi-vertical llamada internamente "OMNI"/"varcreative" (CodeIgniter 2.1.2 + PHP plano).
> Este documento describe **qué hace el producto**, no cómo está implementado hoy, para que un equipo de
> desarrollo pueda reconstruirlo en un stack nuevo sin arrastrar la deuda técnica del sistema legado.

---

## 1. Qué es el producto

**Entrena y Más (entrenaymas.com)** es un **marketplace bilateral de entrenadores/profesionales del fitness**:

- Del lado de la **oferta**: entrenadores personales y centros/estudios ("Centro Especializado") crean un
  perfil público, configuran sus servicios, tarifas, zonas y horarios, y reciben contactos y reservas.
- Del lado de la **demanda**: visitantes buscan y filtran entrenadores por especialidad, ciudad, tipo de
  entrenamiento (presencial/online) y objetivo, ven su perfil, los contactan, los reservan (turnos) o los
  califican.
- Existe además un **canal de e-commerce paralelo**: venta de "Cajas Regalo" (gift cards/bonos), que el
  comprador o el destinatario luego canjea eligiendo un entrenador elegible.

El sistema actual reutiliza una plataforma genérica pensada para muchos rubros (inmobiliarias, viajes,
sorteos, clasificados, delivery de comida, etc.). Para la reconstrucción, **solo interesa el subconjunto
descrito en este documento**; todo lo demás (hoteles, autos, clasificados, delivery...) debe considerarse
ruido a descartar.

---

## 2. Actores / Roles

| Rol | Descripción |
|---|---|
| **Visitante anónimo** | Busca entrenadores, filtra, ve perfiles, agrega a favoritos (solo de sesión), contacta, reserva turno, califica, compra caja regalo. |
| **Entrenador / Profesional individual** | Se registra (plan gratuito o premium), gestiona su perfil, servicios/tarifas, horarios, recibe leads y reservas, ve/gestiona sus canjes de caja regalo. |
| **Centro Especializado (gimnasio/estudio)** | Variante de registro con datos de "centro" en vez de persona física; mismo flujo que un profesional individual. |
| **Cliente/Lead** | Se crea automáticamente (sin registro explícito) cuando alguien contacta, reserva o compra; se le puede loguear luego para checkout de compras. |
| **Comprador de Caja Regalo** | Compra en el carrito/checkout; puede o no ser el mismo que la canjea. |
| **Administrador (back-office)** | Gestiona entrenadores (activación, destacados), productos/cajas regalo, pedidos/facturación, reservas, leads, blog, sliders de home, textos editables, cupones, suscripciones de pago. |

---

## 3. Funcionalidades por área

### 3.1 Búsqueda y directorio de entrenadores

- **Home**: buscador destacado (tipo de profesión + localidad), accesos rápidos por especialidad,
  carrusel de entrenadores destacados (foto, badge "Perfil Verificado", rating con estrellas, especialidad,
  ciudad, precio "desde $X"), botón de favorito, mini-blog (3 posts), slider de imágenes editable,
  carrusel de anunciantes/marcas colaboradoras, newsletter.
- **Listado de entrenadores** con filtros combinables:
  - Tipo de perfil: Verificado / Todos
  - Tipo de entrenamiento
  - Tipo de servicio (presencial / online)
  - Especialidad/categoría
  - Objetivo
  - Localidad
  - Tipo de profesión/título
  - Paginado por "cargar más" (scroll infinito vía AJAX, evitando repetir IDs ya mostrados)
- **Vista mapa**: mismos filtros, resultado en mapa (lat/long) en vez de lista.
- **Landing pages SEO programáticas**: combinaciones pre-armadas de especialidad × ciudad × título ×
  objetivo, con copy propio (H1/H2/texto comercial/texto pie) y bloques de enlazado interno automático
  hacia combinaciones relacionadas (mismo objetivo en otras ciudades, mismo entrenador en otra
  especialidad, etc.). Este es un módulo de SEO importante para tráfico orgánico — vale la pena
  preservarlo en la reconstrucción, aunque puede simplificarse su implementación.
- **Ficha de entrenador**: galería de fotos, badge verificado, rating + cantidad de reseñas, título(s),
  matrícula/número de colegiado, dirección, botón de WhatsApp (oculto si el profesional gratuito ya
  alcanzó su tope mensual de contactos), sección "Sobre mí" y "Formación académica", tabla de tarifas
  (servicio, duración, precio), formulario de contacto (nombre, email, teléfono con código de país,
  mensaje, reCAPTCHA + honeypot anti-spam), botones de compartir en redes (solo perfiles verificados),
  widget de reserva de turno (ver 3.2), entrenadores relacionados al pie.

### 3.2 Reserva de turnos (booking)

- El entrenador configura servicios (nombre, duración, precio) y disponibilidad horaria por día de semana.
- Flujo de reserva en 3 pasos: datos de contacto → selección de servicio/dirección → selección de fecha
  y horario disponible (consultado en vivo contra la agenda del entrenador).
- Al confirmar: se valida que el horario siga libre, se crea/reutiliza el registro de cliente/lead, se
  registra el turno, se notifica por email al cliente (con copia al entrenador) y se genera un cargo en
  la facturación mensual del entrenador (ver regla de negocio de "pago por lead").
- Cancelación de turnos, listado y calendario para gestión administrativa.

### 3.3 Registro y planes de entrenador

- **Plan gratuito ("Perfil básico")**: alta con datos de contacto, ubicación (país fijo, provincia →
  localidad en cascada), bio corta, credenciales. Queda **inactivo hasta aprobación manual**.
- **Plan Premium (mensual o anual)**: mismo formulario + selección de ciclo de facturación + cupón de
  descuento validable en vivo. Al confirmar, se abre un flujo de pago con tarjeta (pasarela de suscripción
  recurrente) para dar de alta el cobro. Reglas de prorrateo según el día del mes de alta (cobro completo,
  bonificación parcial o mes gratis según el día de alta). Al aprobarse el pago, el perfil se activa y pasa
  a "verificado/destacado" automáticamente.
- Todo formulario público (registro, contacto, reserva) lleva **honeypot + reCAPTCHA** como antispam.

### 3.4 Cajas Regalo (e-commerce de bonos)

- Catálogo de "Cajas Regalo" (productos tipo bono/voucher, con variantes tipo talle/tamaño reutilizando
  el motor genérico de e-commerce), carrito y checkout estándar (múltiples medios de pago).
- Al finalizar una compra se genera automáticamente un **código de activación único**.
- El comprador o destinatario ingresa ese código en "Activar caja regalo" y luego elige, de una lista de
  entrenadores elegibles para esa caja/categoría (o uno pre-asignado por la caja comprada), a quién
  canjearla, dejando nombre y email.
- El sistema envía por email un **código QR** para presentar en persona ante el entrenador elegido.
- El entrenador, del otro lado, "acepta" el canje cuando el beneficiario se presenta.

### 3.5 Reseñas y favoritos

- Formulario público de calificación (nombre + comentario + estrellas) por entrenador, sin moderación
  previa — se publica y pondera el rating inmediatamente. **Decisión a revisar en la reconstrucción**:
  considerar agregar moderación o verificación anti-fraude.
- Favoritos basados en sesión de navegador (sin cuenta), se pierden si expira la sesión — considerar
  si conviene persistirlos a una cuenta de usuario en la reconstrucción.

### 3.6 Blog / Noticias

- Listado con filtro por categoría y orden (nuevos/alfabético), paginado.
- Detalle con galería, compartir en redes, sección "más leídos".

### 3.7 Panel de administración (back-office)

Gestión de:
- Entrenadores/centros: aprobación, marcar como destacado/verificado, edición de perfil.
- Cajas regalo/productos: alta, imágenes, variantes, asignación de entrenadores elegibles por categoría.
- Pedidos/facturación: estado de pago, medios de pago, exportación a Excel.
- Turnos: listado, calendario, cancelaciones.
- Leads/clientes: listado, etiquetas, filtros.
- Blog: alta/edición de posts.
- Contenido editable del sitio: sliders del home, bloques de texto (tipo CMS in-place), cupones de
  descuento.
- Suscripciones de pago de entrenadores premium (ver histórico de cobros).

---

## 4. Reglas de negocio clave (a preservar)

1. **Perfil gratuito con tope de contactos mensuales**: cada entrenador gratuito solo puede recibir un
   número limitado de contactos/mes antes de que se oculten ciertas acciones de contacto directo (ej.
   WhatsApp); superado el tope, se lo empuja implícitamente a pasar a premium.
2. **Cobro por lead/reserva a entrenadores premium/con turnos activos**: cada reserva o contacto genera
   un cargo acumulado en una factura mensual del entrenador, calculado según una tarifa que el propio
   entrenador configura por localidad (modelo tipo "puja" por posicionamiento en esa ciudad).
3. **Prorrateo de suscripción según día de alta del mes** (cobro completo / parcial / gratis según el
   tramo del mes en que se registra).
4. **Alta gratuita = pendiente de aprobación manual** (perfil inactivo hasta que un admin lo active).
5. **Código de activación único por compra** de caja regalo, canjeable una sola vez, con selección de
   entrenador limitada a los elegibles para esa categoría/producto (o preasignado).
6. **Búsqueda multi-filtro combinable** sobre relaciones muchos-a-muchos (especialidad, tipo de
   entrenamiento, modalidad, objetivo, ciudad, título) — es el corazón del motor de descubrimiento y debe
   soportar buen rendimiento con paginación y "cargar más" sin duplicar resultados ya vistos.
7. **SEO programático**: páginas de resultados filtradas por URL amigable (ej. `/entrenador/ciclismo/madrid/`)
   con copy y enlazado interno auto-generado combinando dimensiones de búsqueda.
8. **Antispam en todo formulario público**: honeypot + reCAPTCHA.

---

## 5. Modelo de datos (entidades inferidas — para diseñar el esquema nuevo)

No existe un dump `.sql` en el repo; el esquema se dedujo leyendo las consultas de los modelos. Entidades
principales a modelar desde cero (nombres conceptuales, no los nombres de tablas legados):

- **Profesional** (persona o centro): datos de cuenta, ubicación (lat/long), bio, credencial/matrícula,
  estado (activo/inactivo), nivel (básico/verificado-premium), redes sociales, imágenes de galería.
- **TaxonomíaProfesional** (N:M con Profesional): especialidad, tipo de entrenamiento, modalidad
  (presencial/online), tipo de título/profesión, objetivo, localidad de cobertura.
- **ServicioDeEntrenador**: nombre, duración, precio, dirección/ubicación asociada.
- **DisponibilidadHoraria**: por día de semana / rango de fechas, por servicio.
- **Turno/Reserva**: cliente, servicio, profesional, fecha/hora, estado, origen.
- **Cliente/Lead**: datos de contacto, tags, origen del contacto.
- **Contacto/Consulta**: registro de cada interacción (para el conteo de tope mensual).
- **FacturaMensualDeEntrenador**: acumulado de cargos por lead/reserva, tarifa configurada por localidad.
- **SuscripciónPremium**: ciclo (mensual/anual), estado, historial de cobros, cupón aplicado.
- **Producto/CajaRegalo**: nombre, precio, imágenes, variantes (talle/tamaño), categoría.
- **CategoríaDeCajaRegalo ↔ EntrenadoresElegibles** (N:M).
- **Pedido/Factura de compra**: ítems, medio de pago, estado, código de activación generado.
- **CanjeDeCajaRegalo**: código, entrenador elegido, datos del beneficiario, estado (pendiente/aceptado).
- **Reseña**: entrenador, autor, comentario, puntaje.
- **Favorito**: solo si se decide persistirlo a cuenta (hoy es de sesión).
- **PostDeBlog** / **Categoría de blog**.
- **BloqueDeTextoEditable** (CMS simple) y **SliderDeImágenes** para el home.
- **CupónDeDescuento**.
- **SEOLandingPage**: combinación de filtros → slug, copy, metadatos.

---

## 6. Integraciones externas

| Integración | Uso |
|---|---|
| **Pasarela de pago para suscripción premium** (hoy Paycomet) | Cobro recurrente mensual/anual del plan premium del entrenador, con prorrateo por día de alta. |
| **Medios de pago del checkout de cajas regalo** | Debe soportar al menos: pago con tarjeta (ej. Stripe), Mercado Pago u otro procesador local, transferencia bancaria, pago en sucursal/efectivo contra entrega, "a convenir". |
| **Email transaccional** (hoy SMTP de Gmail) | Confirmaciones de registro, reservas, contacto, compra, y envío del **código QR** de canje de caja regalo. Usar un proveedor transaccional real (SendGrid, Postmark, SES, etc.) en la reconstrucción en vez de SMTP de una cuenta Gmail. |
| **reCAPTCHA v2** | Antispam en formularios públicos (registro, contacto, reserva). |
| **Import/export Excel** | Alta masiva de productos y exportación de pedidos/reportes desde el panel admin. |
| **Mapas** (Leaflet/Mapbox u similar) | Vista de mapa del listado de entrenadores. |
| **Analytics / Pixel de Facebook / verificación de sitio de Google** | Configurables por variables (hoy inyectadas como snippets crudos por tenant; en la reconstrucción, integrar de forma nativa en vez de HTML crudo inyectado). |

---

## 7. Alcance recomendado para la reconstrucción

**Incluir (core del negocio):**
- Todo lo descrito en la sección 3 (búsqueda/directorio, ficha de entrenador, reservas, registro/planes,
  cajas regalo, reseñas, favoritos, blog, panel admin).
- Las reglas de negocio de la sección 4.

**Dejar fuera / no heredar del sistema actual:**
- Toda la infraestructura multi-tenant genérica (inmobiliarias, viajes, sorteos, clasificados, autos,
  delivery de comida, hoteles) — el sistema actual comparte código con decenas de rubros no relacionados;
  la reconstrucción debe ser un producto dedicado, no una nueva instancia de la plataforma genérica.
- El modelo `Profesional_Model`/tablas de una plantilla previa de psicólogos ("Psicoweb") de la que este
  sitio fue una reutilización visual — hoy convive taxonomía con nombres de otro rubro (ej. "obras
  sociales", "tipos de pacientes") que no aplica a fitness y no debería replicarse.
- La página estática `planes.php` (estaba desconectada del sitio real; los planes reales están en el
  flujo de registro).
- El backdoor de debug que envía SQL fallido por email a una casilla personal — reemplazar por logging
  centralizado.
- El envío de textos/scripts de tracking como HTML crudo inyectado por variable de base de datos —
  reemplazar por integraciones nativas configurables (analytics, pixel, etc.) con sanitización adecuada.
- Confirmar `db_debug`/verbosidad de errores en producción — no exponer detalles de errores de base de
  datos a usuarios finales.

**Decisiones a validar con el negocio antes de reconstruir:**
- ¿Se debe moderar reseñas antes de publicarlas? (hoy no se modera).
- ¿Los favoritos deben persistir a una cuenta de usuario en vez de a la sesión del navegador?
- ¿El tope de contactos mensuales para perfiles gratuitos y el modelo de "puja por localidad" siguen
  vigentes tal cual, o cambia el modelo de monetización?
- Confirmar qué medios de pago del checkout de cajas regalo se quieren mantener (hoy: Mercado Pago,
  Stripe, transferencia, pago en sucursal, a convenir; PayPal parece legado y probablemente descartable).

---

## 8. Requisitos no funcionales sugeridos

- **Multi-idioma**: el sitio público hoy es 100% español; no hace falta soportar i18n salvo que el
  negocio lo pida.
- **SEO**: crítico — preservar URLs amigables, metadatos por página, y el mecanismo de landing pages
  programáticas (sección 3.1) es una fuente relevante de tráfico orgánico a día de hoy.
- **Rendimiento de búsqueda**: el filtro combinado de entrenadores debe escalar bien (paginación,
  evitar N+1 al traer taxonomías relacionadas).
- **Seguridad**: 
  - Hashing de contraseñas con un algoritmo moderno (no MD5, usado hoy).
  - Sanitizar cualquier contenido "editable por admin" que se inyecta como HTML crudo en el sitio público.
  - No loguear ni exponer errores de SQL a usuarios finales ni a emails externos hardcodeados.
- **Panel admin**: puede ser una aplicación separada del sitio público (arquitectura recomendada) en vez
  de compartir el mismo framework/base de código que el frontend público, a diferencia del sistema actual.

---

## 9. Glosario (para evitar confusiones heredadas del sistema actual)

| Término en el código actual | Qué es realmente |
|---|---|
| "Productos" | Cajas Regalo / bonos canjeables, no productos físicos de fitness. |
| "Servicios" (página) | Pantalla de aceptación de canje de caja regalo por parte del entrenador, no un listado de servicios. |
| "Formas de pago" (filtro de búsqueda) | En la UI se muestra como "Objetivos" — es un filtro de objetivo de entrenamiento, no un medio de pago. |
| "Planes" (página estática) | Desconectada del flujo real; los planes reales están en Registro. |
| "med_*" (tablas de taxonomía) | Resabio de una plantilla previa para profesionales de salud/psicología; conceptualmente equivalen a "especialidad/título/modalidad" en el rubro fitness. |
| "Consulta" / `crm_consultas` | Cualquier contacto (formulario, WhatsApp, reserva) que cuenta para el tope mensual gratuito y para la facturación por lead. |
| "Clienapp" | Flujo de registro silencioso de un visitante como lead antes de abrir WhatsApp. |

---

## 10. Notas sobre el proceso de este documento

Este documento fue elaborado leyendo el código fuente actual (plantillas públicas en `templates/entrenaymas/`,
modelos en `models/` y controladores AJAX en `sistema/application/controllers/`), ya que no existe
documentación funcional previa ni un diagrama de base de datos. Se recomienda validar con el equipo de
negocio los puntos marcados en la sección 7 ("Decisiones a validar") antes de comenzar la reconstrucción,
ya que algunos comportamientos actuales (reseñas sin moderar, favoritos por sesión, tope de contactos)
podrían ser deuda técnica accidental más que requerimientos deliberados.

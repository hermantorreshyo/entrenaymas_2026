<style type="text/css">
@import url('https://fonts.googleapis.com/css?family=Ubuntu:400,700&display=swap');

#converse-chat-launcher {
  font-family: 'Ubuntu', sans-serif;
  position: fixed;
  bottom: 10px;
  right: 10px;
  width: 60px;
  height: 60px;
  background-color: #25b249;
  border-radius: 50%;
  cursor: pointer;
  display: block;
  -webkit-box-shadow: 0px 0px 20px 0px rgba(91, 91, 91, 0.5);
  -moz-box-shadow:    0px 0px 20px 0px rgba(91, 91, 91, 0.5);
  box-shadow:         0px 0px 20px 0px rgba(91, 91, 91, 0.5);
  background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAOzSURBVHja7JhPiJVVGIefezXLmWrGbGpGK5MyIswWWtAfZIbKphIXUS1Kg2oTtAppEdXgQLYJo0UTQYtqFzJJaIy1yYVJGSikhjojUliNOUxNoxbq6NPmvXT6GJ17v/kuIdwXDvfc8+c9z/ee853z+05J5WKzMhehNaAb0A3oBnQ+m1mQn2ZgMbAcWApcH2XngD+AQ8A3wNfA4SjPbaVpHi5l4H7geaAbaJmi/WHgE+BjYPD/gF4E9ABPJctsHNgDHAFORnkrcEvMRMV+Ad4B+oC/ax5ZzZMeVIf81/aqL6m3q3PUUtL2EnWeulx9Vx1N+vWrbbWOnwf4EXUkBh1X16lX1NB/sbo5Af+qVvBagZeqwzHYT+qKnDM1S+1VJ8LXZrWlHtCt6rcxyLB6X07gNPUkEe+pB/TL4XxCXZ2Ul9U16hvqXTnAN4bf39UlRULPS168/gCt1L2QRGtAnV0j9EL1t+j/XiydQqCfCaenMtFsVw8k0MfVO3JEuzf6j1bTv1zlabcy8tuB75K6G2K/rtjlwBM59vx+4ARwFfBwEdqjDVgW+S8zdWeBM5myphzQPybBuLcI6IXA/MjvzdQdAfYn/weA9Tmgj4c+qcxeIZGeBZwGjmbqjgGbkv8HgNGcsmA4fmcUAV1RghORsvZB6A2A54AHckJXlplFQP8Vji6LFy1rR4FXYn23Au8Dt00mzqYY58oq21UF/XO82eULrLcBoDfyNwGfAp1JfUfsEG8CC87zQNdF/mQRKu9adVfsoxum0BN9yZ79p7pe7VI3JeVDoQibM2N8H/UfFXG4zFTfDoeDatMF2l6qvuV/bczJbVHSb0UintYUdSJ2JU6fraL96tDY57PdmYevzMQ+taMo6GZ1Szj+QV1QRZ9rQpdsVU9k9POdSbtVSUBezHxATFvlPR2Oz6kra+jXFDr8SbUzo5tvVg+G313q3KKl6evhfH+eT6RJUoe6LXyeVruL1tMt6o4YoC9TV8oBvCz5oFBdW4/PrXvUMxGRu9WrY7lsjGg9HrvMVH7aYsaOJcCv1frQ1VzWlICH4jgfB9aGEmtP2nQCO4HPQq39CpyKA6kp5GsXsCo5oIaBV4EP63Hv0R6SdEmmfCz0dUvcLKU2GqfoDGBOaPJUFvQDGxLNUvi1WHcCPAbsAD4P4EPA7IjiYwE/H5gbKQUdBL6I2dhZ72uxdcCjwFZgC3AwlgmTiP8O4FbgxpiBs8AIMBTQI9O9xyviLq9x1duAbkA3oBvQ9bV/BgBa8danK1QwPgAAAABJRU5ErkJggg==');
  background-position: center center;
  background-repeat: no-repeat;
}
#converse-chat {
  width: 240px !important;
  position: fixed !important;
  bottom: 65px !important;
  right: 45px !important;
  margin: 0 20px !important;
  z-index: 9999999999 !important;
  display: block !important;
  /* reset css */
  padding: 0 !important;
  border: 0 !important;
  font-size: 100% !important;
  font: inherit !important;
  vertical-align: baseline !important;
  list-style: none !important;
  border-collapse: collapse !important;
  border-spacing: 0 !important;
  content: '' !important;
  content: none !important;
  quotes: none !important;
  line-height: 1 !important;
  text-align: left !important;
  box-sizing: border-box !important;
  -webkit-box-shadow: 0px 0px 20px 0px rgba(91, 91, 91, 0.5);
  -moz-box-shadow:    0px 0px 20px 0px rgba(91, 91, 91, 0.5);
  box-shadow:         0px 0px 20px 0px rgba(91, 91, 91, 0.5);
  border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px;
  transition: all 0.5s ease-out;
}
#converse-chat-cont { border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; overflow: hidden; }

#converse-chat.chat_izquierda {
  right: auto !important;
  left: 45px !important;
}
#converse-chat.chat_izquierda #converse-chat-launcher {
  right: auto;
  left: 10px;
}

.converse-chat-input > input {
  text-align: left !important;
  max-height: 150px;
  -webkit-box-shadow: none !important;
  box-shadow: none !important;
  box-sizing: border-box !important;
  outline: none !important;
}

#converse-chat .converse-chatbox {
  height: 230px;
  display: none;
}

#converse-chat .converse-conversation {
  transition: all 0.5s ease-out;
  border-left: 1px solid #EDEDED;
  border-right: 1px solid #EDEDED;
  height: 163px;
  background-color: #fff;
  padding: 5px 12px 5px 12px;
  overflow-y: auto;
  font-size: 14px;
  font-family: 'Ubuntu', sans-serif;
  font-style: normal;
  font-variant: normal;
  font-weight: normal;
  letter-spacing: normal;
  line-height: 21px;
  list-style-image: none;
  list-style-position: inside;
  list-style-type: disc;
  color: rgb(0, 0, 0);
}

#converse-chat .converse-chat-input {
  border-left: 1px solid #EDEDED !important;
  border-right: 1px solid #EDEDED !important;
  background-color: #fff !important;
  text-align: center !important;
  font-family: helvetica, sans-serif !important;
  text-align: center !important;
  font-size: 9px !important;
  letter-spacing: 2px !important;
  font-weight: bold !important;
  color: rgb(170, 170, 170) !important;
  padding-top: 4px !important;
}

#converse-status-green {
  display: inline-block;
  background-color: #42b72a;
  width: 6px;
  height: 6px;
  border-radius: 100%;
  margin-bottom: 2px;
  margin-right: 3px;
}
#converse-status-red {
  display: inline-block;
  background-color: red;
  width: 6px;
  height: 6px;
  border-radius: 100%;
  margin-bottom: 2px;
  margin-right: 3px;
}

#converse-chat .converse-chat-input input {
  display: inline-block !important;
  margin: 2px 0px 2px 0px !important;
  width: 100% !important;
  border-top: 1px solid #CDCDCD !important;
  border-bottom: none !important;
  border-left: none !important;
  border-right: none !important;
  border-radius: 0px !important;
  font-size: 14px !important;
  font-family: Helvetica, Arial, Geneva, sans-serif !important;
  padding: 8px !important;
  color: #000000 !important;
  vertical-align: middle !important;
  height: initial !important;
  float: none !important;
  line-height: 14px !important;
  background-color: white !important;
}

#converse-chat .converse-menu {
  overflow: hidden;
  background-color: #25d366;
  padding: 5px 8px;
  color: white;
  font-size: 14px;
  cursor: pointer;
  font-weight: normal;
  font-family: 'Ubuntu', sans-serif;
}
#converse-chat .converse-menu-title > img {
  height: 22px;
  width: 22px;
  margin-right: 5px;
}

#converse-chat .converse-menu a {
  color: #fff;
  text-decoration: none;
}

#converse-chat .converse-menu-icon {
  float: right;
  font-weight: normal;
  font-size: 14px;
  line-height: 10px;
  padding: 2px;
  margin-top: 4px;
  font-family: 'Ubuntu', sans-serif;
}

.message-visitor{
  text-align:right;
}
input:-webkit-autofill {
  -webkit-box-shadow: 0 0 0px 1000px white inset;
}

.converse-msg {
  float: left; clear: both;
  border: solid 1px #25b249;
  background-color: #25b249;
  border-radius: 0px 6px 6px 6px;
  padding: 5px 8px 6px;
  word-wrap: break-word;
  display: block;
  margin-bottom: 16px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: normal;
  color: white;
  font-size: 14px;
  max-width: 80%;
  min-width: 40%;
  position: relative;
}
.converse-msg .converse-user {
  display: none;
  color: white;
  font-size: 12px;
  font-weight: bold;
}
.converse-msg:before {
  content: "";
  position: absolute;
  border-top: 7px solid transparent;
  border-right: 14px solid transparent;
  border-bottom: 5px solid transparent;
  border-right-color: inherit!important;
  display: block;
  width: 0;
  z-index: 1;
  left: -10px;
  top: -5px;
  border-radius: 3px;
  transform: rotate(28deg);
  -ms-transform: rotate(28deg);
  -webkit-transform: rotate(28deg);
}
.converse-visitor {
  text-align: right !important;
}
.converse-visitor .converse-msg {
  float: right; clear: both;
  border: solid 1px #f1f0f0;
  background-color: #f1f0f0;
  color: #4b4f56;
  border-radius: 6px 0px 6px 6px;
}
.converse-visitor .converse-msg:before {
  left: auto;
  border-right: none;
  border-top: 5px solid transparent;
  border-left: 14px solid #f1f0f0;
  border-bottom: 5px solid transparent;
  right: -11px;
  top: -4px;
  border-radius: 3px;
  transform: rotate(-23deg);
  -ms-transform: rotate(-23deg);
  -webkit-transform: rotate(-23deg);
}
.converse-visitor .converse-msg .converse-user {
  color: #4b4f56;
}

#converse-chat input::-webkit-input-placeholder{
  color: rgb(170, 170, 170) !important;
  font-weight: 400;
}

#converse-chat input::-moz-placeholder{
  color: rgb(170, 170, 170) !important;
  font-weight: 400;
}

#converse-chat input:-ms-input-placeholder{
  color: rgb(170, 170, 170) !important;
  font-weight: 400;
}
.clienchat-link { text-align: left; padding: 3px 10px; background-color: #f1f1f1; line-height: 10px; }
.clienchat-link a { font-family: 'Ubuntu', sans-serif; font-size: 8px; color: #999; text-decoration: none; outline: none; }
.converse-no-seen {
  text-align: center;
  line-height: 20px;
  border-radius: 50%;
  background-color: red;
  font-size: 12px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: bold;
  color: white;
  width: 20px;
  height: 20px;
  position: absolute;
  top: 0px;
  right: 0px;
}

.chat_user { }
.chat_user_card { display: table; border-top: solid 1px #eee; padding: 10px; cursor: pointer; width: 100%; overflow: hidden; margin-bottom: 10px; }
.chat_user:first-child .chat_user_card { border-top: none !important; }
.chat_user_image { vertical-align: middle; display: table-cell; text-align: center; width: 58px; padding-top: 5px }
.chat_user_image img { width: 48px; height: 48px; margin-right: 5px; margin-left: 5px border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%; border: solid 1px #eee; }
.chat_user_info { display: table-cell; vertical-align: middle; padding-top: 5px; }
.chat_user_info .chat_user_cargo { color: #272727; font-size: 12px; font-weight: bold; text-transform: uppercase; line-height: 18px }
.chat_user_info .chat_user_nombre { color: #929292; font-size: 14px; }
.chat_user_info .chat_user_disponible { display: inline-block; font-size: 10px; background-color:#2fb04e; border-radius: 10px; -moz-border-radius:10px; color: white; padding: 0px 10px; cursor: pointer; line-height: 20px; text-transform: uppercase; }
.chat_user_info .chat_user_disponible.chat_user_red { background-color: #cf1035 }
.chat_user_form { display: none; }
.chat_user_form .chat_user_form_row { margin-bottom: 8px; overflow: hidden; float: left; width: 100%; }
.chat_user_form .chat_user_form_row .chat_user_form_input { height: auto; width: auto; background-color: #f8f8f8; width: 100%; display: block; color: #292929; border: solid 1px #cacaca; padding: 8px; font-size: 14px; line-height: 14px; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; outline: none !important; box-shadow: none !important; margin: 0px !important; text-align: left; }
.chat_user_form .chat_user_form_row .chat_user_form_input:focus { border: solid 1px #265833 !important; }

.chat_user_form_btn_enviar { height: auto; width: auto; font-size: 18px; font-weight: bold; line-height: 14px; background-color: #25d366; display: inline-block; color: white; border: solid 1px #25d366; padding: 15px 45px; border-radius: 30px; -moz-border-radius: 30px; -webkit-border-radius: 30px; margin-bottom: 5px; }

.chat_user_form_2 { display: none; }
.chat_user_form_2_titulo { color: #515151; font-size: 14px; font-weight: normal; line-height: 18px; margin-bottom: 10px; margin-top: 5px; }
.chat_user_form_2 .chat_user_form_row { margin-bottom: 8px; overflow: hidden; float: left; width: 100%; }
.chat_user_form_2 .chat_user_form_row .chat_user_form_input, .chat_user_form_2 .chat_user_form_row .chat_user_form_select { height: auto; width: auto; background-color: #f8f8f8 !important; width: 100%; display: block; color: #292929 !important; font-style: normal !important; border: solid 1px #cacaca; padding: 8px; font-size: 14px; line-height: 14px; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; outline: none !important; box-shadow: none !important; margin: 0px !important; height: 30px !important; }
.chat_user_form_2 .chat_user_form_row .chat_user_form_input:focus { border: solid 1px #265833 !important; }
.chat_user_card_min .chat_user_cargo, .chat_user_card_min .chat_user_disponible { display: none }
.chat_user_card_min.chat_user_card { border-top: none; }
.chat_user_card_min .chat_user_image img { width: 24px; height: 24px; }
.chat_user_card_min .chat_user_image { width: 30px; }

.chat_user_form_row_4 { float: left; width: 40%; }
.chat_user_form_row_6 { float: left; width: 60%; }

.chat_user_form_row_tac { text-align: center; }
.char_user_form_2_resultado { display: none; text-align: center; font-size: 18px; padding: 15px; line-height: 28px; color:#505050; }
#converse-chat.clienapp-v2 { width: 280px !important }
#converse-chat.clienapp-v2 .converse-conversation { height: 345px; }
#converse-chat.clienapp-v2 .chat_user_form_label { font-weight: bold; color: #515151; font-size: 15px; display: block; margin: 0px; padding: 0px; }
#converse-chat.clienapp-v2 .chat_user_form_input { padding-top: 3px !important; margin-bottom: 5px !important; border-radius: 0px !important; -webkit-border-radius: 0px; background-color: white !important; padding-left: 0px; border: none; border-bottom: solid 1px #ededed; text-align: left; }
#converse-chat.clienapp-v2 .chat_user_form_input:focus { border: none !important; border-bottom: solid 1px #265833 !important }
#converse-chat.clienapp-v2 .chat_user_form_select { /*-webkit-appearance: none;*/ background-image: none !important; padding-top: 3px !important; margin-bottom: 5px !important; border-radius: 0px !important; -webkit-border-radius: 0px; background-color: white !important; padding-left: 0px; border: none; border-bottom: solid 1px #ededed; text-align: left !important; }

#converse-chat.clienapp-v2 .converse-menu { padding: 10px 8px; font-size: 16px; }
#converse-chat.clienapp-v2 .converse-menu-title > img { width: 26px; height: 26px; position: relative; top: -2px; }
#converse-chat.clienapp-v2 .converse-menu-icon { font-size: 18px; }

@media (max-width: 500px) {
  #converse-chat.clienapp-v2 { width: 100% !important; top: 0px !important; right: 0px !important; left: 0px !important; bottom: 0px !important; height: 100% !important; margin: 0px !important; }
  #converse-chat.clienapp-v2 .converse-conversation { height: 100% !important; }
  #converse-chat.clienapp-v2 #converse-chat-cont { height: 100% !important; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px; }
}
</style>
<div id='converse-chat-launcher'><div class='converse-no-seen'></div></div>
<div id="converse-chat-cont">
  <div class="converse-menu">
    <span class="converse-menu-title">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABlCAYAAAC7vkbxAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAABweSURBVHhe7V0HdJVVtp5xxpnRcURHfeNzfGscZ1QUBQt2XahYUBkF26gLdGygdAgdEnogoQWkiwSVHkKTKqH3npB208tND+nJLX/73t6HG4ck578lCeHiYmd9S0xyT86/v3POLmef8//qrtgeuAL/wRVC/AxXCPEzXCHEz+D3hLSO7Yn2cX3xdMIgdEgcgo6W4XglaRReTx6DN1PG463UiXgvbQreTwslhOCd1GB0TZmAfyWPxatJgXjJMhIvJA7Ds4mD8Xh8f7SL6y39O/4CvyPkbsK9sV/i/rhepLw+RMJg9EoNxaTsxZiftwbLC7diU8k+7Ks4hejqJKTbrTinlqFat6FKq0GusxAWWwaOVcZiZ9lRRBZHIbxgI2bmLMOIzDn4MHk8Hozvh7ZETJu4rwThsn5cKvgVIa0Jj8b3Rg8a8TNyV2BL6SHE1aSjSClDqVqBCrVKKL1as8GmO+DQFSiGCs3QoBu6gEr/dtL37boTNs0ufrdSrUa5WolzSjmsjkIcr0rED0XbMDprAd6k2dY27ktpfy4FLikhPBuejOuPj1MnY4x1CRYW/ogd5cdp5Ccj054nSGDlGoBAU4Xb0Ig0JirfeQ5JtiwcppkUWbIf02n2BdAM6kxL3EOXcFm7JITwTHgifgA+S5uKsLwIbC89gtjqFOQ5iwQBumE0CwGe5DxBGs26alr6cnC8Mg6rincikAbHu2SHHoztJQaN7BkuFlqckMeIiH8lB2FQ5lxsKzmIPMd5EvxFeInjwfED2aov0qbhZZoxD5ItayliWoQQfpj7Yr8SRnowEbGDbAPPhqaIwbOIwLOJbQcvRQz+d+3PmiIOskHZtGyuLtyO7qmTyLb1Fc8ge77mRIsQ8kTCAAwnIjaVHBC2oYbWcDa+TRFWuJOUVkbGmj2rdIcVGY4cFColYglSydg3Rbh9VVeFExFvS8eK4h3okx4mfb7mxEUn5J2UiVhIbmdiTSZKyMtR6CG9FR7trJAERxq2Ow5hqXMLZioRGKctQ299Md4y5uM1fQ5e0WfhRW0GXtJmopM+G52NuehmLMQAfQkmaCswS1mL7x3bsNtxHCnOTPLAHD7NIPbYCsgJOFGVgFn5kXjNMgIPXKTZclEJ6U5Ge+W5XciiWcGP70kFbGDZtU1zWnHEeRZr1H2Yom3Ap8pidFZC8ZQahLbaMPxDG4Sbjb74jdELv4Ic1xq98Re9H+7SAtBOHY5nlLHoosxAD2UJgvSN2KIcQbTTgly1EDUUw3gjvIxl2nOxtGgLPiLPkJcx2XM3Bc1OCNuLByioe4Oi6EgK4IqUUtfjmAvPmjK9EolaNiKUY5hu34A+tvl43hmE2/S+UoU3Bb8z+qCrPRgjbEux0LkDP6kxyNWKRHDJdsid8KzlQHRV8U/4NC2USCHS6+mgKWhWQpiMB8mHfztpDM5UW4S/bya1hlcxFOQqRdhiP4Te9vlSBV5s/EMbjpk1q3DCkYgSrULYH3YW3AkTt6f8JPqmz0SbZly+mpWQZxIGYljWfFjJg+LpzaPJTPiBKrUq7LQdxgDnfDyijUIrY4BUYRcbv6Xl7X/0QXhGC8QUxwocc8QKZ8Gd8GDibMHZ6lSMzlwoXGOZTnxFsxHyUuIwzKQgL9GW+bPraSa5ahGiyMBOUpahgxqM/9MH4xpSikxZLYVfkz26jvpwtzYCXdXp1LeVOGA/LZYxs2fh7/IqkFCTjsk5y/Bc4lCpbnxBkwnhZeoRGh3T8laLVIT7iQ6kqtmYo2zFm2Rg79OG0Hp+aYmQoRU5DG3IeXjfORuRyj5YtUKRMzMTdkaYlIDMeRT49pfqyVs0mZD7475Cz7QZOFuTZkoGjzDudI5eiHnOH/G0OkGqCH8DG/8uzmAsV3YhXcuDk+ydO/mxZD/+kxrSJJe4SYTcF/slXrYMg4WWKcUk/XHecKso0Esx1bkC9+gjpA/vz3hYH4oZaiTN7lwxsNwtYRHn9qAzxSkyfXmDJhHSKSkQ28uOiODNrJMchB1XLXhHn4LbjABcTaNO9tD+DF5WbzIGoTMFnRxYsmcoE9ZALjk0y8klbkezpDH5r0YT8i5F4OFF20SK3Mx3L1ZLMU/dhWf1qfRA/XCVm0DucsANRMprWhg2Og6iWCtzPWVd4URpit2KkNzleLgRMUqjCOmaPE6Qke3Id3WjrvBs4XzVXCLjUT1Y+nCXK3iGv6qEYovzJMrJbZcJu8PxZFOHZC3EkwmDpDo0g0+EnPeo+mJ2/lqxf2AmnDg8qiTgGT1U+lC/BPRwLsFhxWKam2O7eagiGp+kTUXbuF5SfcrgEyG81/1FymTEVKe4/mxD4eWrWC/HB84puFnvJ32YXwr6qyuRpxS7nlwu4YWb0DlplFSfMvhEyANxvbG0cDPynOadyNTyEVSzGLdS5Hu52wxPeFIZg7n29a4nl0t0lQUDKD6R6VMGrwl5iIK/T1Ini0ic0yIySVazMMuxEXeqQ0Q6QvYQvyS0ohWgozIZ2+2HUWPI83ZcYLG6OArvJo+V6rU+vCbkeYo3lhRsRKVW7fpTdUV4VM7teEIZL+38LxV/1gfibecMnHGmkDGXk5JABn5q7gqpXuvDK0K4uOzjtFDEke0w2//mhNz7yhxppz3hFgzC3RiFBzEe7RH8M+5DEG5FgPQz/oQ/Gv2xyL4FeZp8KecSpB1lx0ShX2uywzId18IrQrpQALg4f4Or+brCLi6nq+c7NqC9MlraYXf4PfqgG2biO2zGQZxBPNJ/xgbso9+Yh6vh38sfp1i6KSE4pSWZxmRcQzAndzUe9ZDr8oqQgZnzcaoy3tV0XWEyjtpi8JI6Fb/3MQpnMr7AbBxBAipRAzsccEL5GdWw4SgS8QbNnGvR/BtVzQaRKe6HEOd6ZKi5Ls3UFc4KczXLe0lj0dZNrssjIR0TB5PrtkVsrdYXTrOX61WYYl+Gu7Th8s6agJep7jQzjrrIMEtM5qII07EKf6bfl7XjT3hbC8V27Zg0r8e64j2WqdYf8BwtXTJdMzwS8nlaCPaWn3Q1W1eq9BocssfgBWUCrvcx5miN0ViB7agiMtwJ//ygEYPHjSBc6+d5sL8ZAQjSViKFvE2ZsP3dVnIAb7rxuDwSMtYaTsY81dVkXcnRCjG1agV5Gr6N3uvQD50wjeZGmlia3IlGX4VGKfoac3E7PbCsPX/CazRL1qt7Xb2vK5wpTiSPqzs5SGaJR7eE8Ie+LdwqSmDqC0/Bs1oa3rSP8XnktqfZMQNrXC15FiZtHXbhYYyTtudPaI0hGG0sFca9fgac/5+z34OzF6BtnNyOmBLCZDwd1w97yk8LZutLiVqOlc6Dggze/pR1zgydEIK1pGBvRYOOVFjRkWiUtedPYI+wgx6KdMXaYJeRCWF8V7gZbySNlurdlBDOW31JUyvWZLlKUbIQYo+UdsoTPsAcHCIX11vh0msbeWAzsBHtECht05/QVh+LKOUobCbR+36yyV+kz5Dq3ZQQPjDzdd4a0xT7QSUaPR2NCwR74XtkwDxbbCaHcRbvEZmyNv0JdxrDEaavQrkhT89n2HMwimyzTO+mhHBh9LbSw+SqVbia+a9wen2ZugdPq40brUNpwaog/8lXyUMxhmM1biCnQNauv+AmDMD7xlTkGMXSUiiusJ9VsF56vM6UkEcooky2Z4vAr77wLmGAtkbaGW8wkmJwDgIbI2uwF8+RUyBr159wvTEIx7UU2A35c64v2YeuEjsiJeR+sh+8UZ/rLG7gKbBE2y3o7lwg7Yg3GESjvBjyLVBPsoycgcdonsja9Sdwfmu5shNFeomr53Vld/kJ9CQbXV/3UkL4SNdnKZNM63J324+ji7PxHk9PLEUy5MGTO8lBAYZhpd8vWYxrjH4IVVYhU89z9b6uHK04i6GZcxvoXkoIHx8elvG1OCQpkx8d+9FJmSLtiDd4C2HYiWOu1jwLe1lsc8KNn/CsMUnapr/hD0ZfDFEXIkmXD7zoqiRMkBh2KSF8JnxKdrhpfesaR5RIl8g64g2eNMZgruF+p+1CUaBiP2LQ1ZiGG+lBZW36GzjR+pk+HWcNedhgqcnAjNxVDXQvJYRrVBfmRUgTiizhjs14SgmSdsQb3G4MwFfGfFKzJka/O9Hp6xzNjw8RilvQX9qeP+JqozdeN4Jw3Eh0PUld4XMmiwo2NdC9lJAXyaCvKtomCuBkMkeJxMNq4ysQ+aDNU0Yw4siSeMplsas7m9zkv2IwrpK05a/geoJ/GgMRZcS6nqSu8LHs5cVRDXQvJaQTuWPbSg+I2iqZBKsr0FobLO2It/A228uEzCFCbroM0u/1cQ16I0I/Lt1lZXOwkeK8+rqXEtI5eQwOV0SLRJhMArRw3KY3bfm4EQPRBdNxEklia8pMmLBDFKG/iElodRl4V/WxWN2LCklBHet2V8WZBrqXEvJG8jicroo3rS7pbSzGzc2wN/FHUvAsrEcmzQNOIMqEbUgFqsmCrMDduPwKtedRLMKJ2PrCAfeBytgGujedIUfKzWfIKP173KEPlHbAF/yWpvSLCKSF67jbpYuN/2kk4CWaUVfj8irWNp0hGs2Qci9nCNuQ7SXmNmSmGiFOw8o64CuuIQUPxLc4A4ur9YbCnhjvtq/DAbxMc0XWjr9iFdkQ2ZYuV6JsKj3UQPdSQoSXVUgG18TLWuLcjCeb4PbWxx0Uf08gdWfT0uVOilGK5diDNzBN2o4/gb2su8nL2mnEQHaAlDf9VhR76WWdj0PWUhwiL4pb69iFjspEaUcai9do5EeSshX6chebcNHDGgoTXyAj/wc/rkThOOQVPRBHTeKQLIpDvvE2DnkmIQCh1u9MI/Xt9kPo7GzepeOfFGf0wQKk0jzhyNydFNJM+Q7b8AjG4tpGeF5Xke36J0biTgwXqfKrJb/TVHCt1kdaCKINeWF6ki0DYXmrG+heSsjj8QMwImOuuApDJgftp/GOM0zakaaAl64pWCYUbuZ11QrblHBsxqMYh98Rlb+WtCcDk8ExzXxsRBhW4T2amX9HANmyfmL71dt2PIFzWQHqPFj0TFeP60pMdRIm5ixtoHspIXyqtmfKFHGTm0xOOxLQzdn8h/zZ6/orhpCi1tI8ke9U1gova1zPtRVH8SE5zzd56X3dQbNiClbS0scbAFU0I3PxE0VDk8mtfgVjqJ3miXU42ztR+QHpunxnlK8gHCapipcSwqdIu1pGU3gvz+UnOTLwufKttCNNxW+IlHsxilS8CelebPOWUpSylwLHYCLxRZot17ixK3dhKPrjG2SQ88DLIlsq/i9nkjOImL2IFvv2H9Fff7iJMQ/vh4Qr21CgN6zYYeFat97p0xvoXkoIXwz5bMIgce5cdkKolAKdUVoE/dGLV3P7OPlds7FFKMqTcMlpCqxiN/E/WEjLWBCN9LqZhFa0pn9hLMIRyEtia11rnpkHEYPF2IHu1NZz1I8/k2N+YVve4E/kYe1VY1Gtyz3VDSX78VZSUAPdSwlhtIvvi93EolnG9xstCveqQ6SdaS48R4vLIgobS1Du0aZwRM/EHKHZMpP8tXdo4ePq+dvJWWC8ZoRgnXFMzApPwr/Dy+FJCkaX0qB4jdxsX3JpnHq/3whEmlEo3VPnDMiiws14QlJ4bUoIV53MyYswrTo54DiDL6pnSzvUnGhDSl1H7nA5LSusdG+ER3smzawN9LmRNMoZZ5Ds0XurL0wMgzfT3sB0af9k+JsxBMFaOEoNuZdqJZ0G5nwn1bspIVyX1SttqmldVprDimk1G6Qdak5wquROUulGWkh4pngjTAgrn2cM76UwHPQdd/GNTGoJ4SK9HjRXZP2ToY0ehI3OPag25Pdw7S8/hR6+1mVx5SLfHLqt7Dj4RrX6UqPbsUuJRhs7XwZwcc9vsPfVntbyH8hGFJJ6vZVahdaisZJFTkAv+uuyvsnwmB6MZCXD9IIBvmDzzaRAqd5NCakFr3X5Joc8k1UrPq+cjj+10GnbFzAVc8jRjaMxy2VEvo74xkoazRAuzJD1qT54ueqtLxHGvL794AoerskabV1ievGZR0JE9XuNfNnKV4sxryISN2tk8Hys720sHifXdiIFdCdhEZGEr3ahMXIGiegG78qeOukhWKvJ65a5AJurFrunT5PqmuGRkE/TQkQNkSxBxrd/7q+Oxt+dI3GV3nLHzm7BAAoGJ5MvtY8ilSJBircG3xfhGajS12rsErkzWV8uBN/y0Ef9HilKtquFusIhRFTZEXRNGS/VNcMjIY+R+zunYL20pJSnJF8vMbVqFe5uwh67r+C99esoAPwLRfVDaCnZS/OlgOZLcwuTcYzc6FfJw3IXcNaiixqCberRBlXvtcK2eEF+JF60DJfqmuGREA4SA7LcnTHUcMZuQTdnGG7RWn6L9W8YhqcoTv+EQrltOCwMcGPLVC8UjnuKyDt7i6yWt8fpxioRSCO7KhPe7EusycB7KZPEmxlkumZ4JITxTvI48gy2uJquK2yoeOlabOfSoEt3oIaj6dfJ6HNl/WocQCwZ/nzyyHiU+2r8yygsPEw2ahAi0MoLMviShGfVCdiqnhJXAsqk0FkizoU8Sp6rTMe18IqQpxMGYETWQhG1yyJPljhHCvo6w3HTJbrI8kK8QvYlEMsRbuzGESMR6UaeiGE4NWJGjk7f57wY57m24ARFP99J25aBE4lT7auRpcmDaJ4dp6os+Jxvm/NwEY1XhDBeTxqDA2WnxF6wmWzVDuMt1X9KPa8jI9ue3NARxiKsxW4kUPxejhpx+IdRQ6imBa6S/o+Xp/XkJIzEIjyCUdL2ZLiKZsetxlDsd8aSSyvPW6XZrJiXv0Gq1/rwmhBP5aUsyUomJjvWSjt+KcBH7X5PCruBZi07AHeSop+n2TORHAHGGHyL/mQjuiIEd2A0bqXfuYE8ON5fkbUnwz16IH6wR6FUrzRdPX4qO4pPUydL9VofXhPClwW/bBmBDef2otikKj7emYpx9hXSjvsDar2zf2CowJ1EwO1kI25s5B7I49p4TFPWo1ArkZ7DZEm2ZWJ8zvde34DtNSGMtnF9EHluDxEi37japZ5E9yYcU7iccKc+GGO11UjR5DEHOzuc1V1ZtEM4RTJ9yuA1Ief3SAIQXZMsrdcSKWVtB9ppQ6UP8EtDN202DugxrqdvKDxjUm3ZGJI5H4/HeX/3oteEPEyzo0eq+bZutpKP3qr3CbjLFmSX/kg2aa1+0vQeX85qcAnVgvy1boNAGbwm5PnEoVicv960NGiv4wS6KjPlD/ELApPxg2M/CrQysSzJhM9g8qXKHcnm8jaGTJ9m8JoQflHjicpY03rfMHUdLVeXX+2tt7hDDcCHzjBE6qcEGWbpEfZCo8pPivtM7jO5rcEdvCKkHQUzPdNnolQpFxnLC4VdvWK1DB9rC8i9vDxON/mKB5VAjHQuwz41WhBhNjO4ZPQnIqNHxiypHr2BV4R0TByCkJxlrj9bV7iK+7g9Ds9oIdKHuVzBh4pupAj8Xi0Q4+0rEaukekzAJNuyMCz7W6kOvYVXhHySGoqt5w64/mxdsRvkXdnWoY1mHt1ygMb5Hi4e4/KY642BAvz/vt6T0hLgvv6vPghd1AlYUxOFQlVeDlUrPGP48uRx1nA80cQ37nhFyAjrEhHgyKTSsKGLNlmMJtnDMa4nxXdQR2CwbQHCbdvFBcSHNQuG1ixGK/3S577q43llEubZ1yPWmYYKvco06KsVJmMarSAdE4fhnia+W9cjIY/G9cX8gk2o0RpmMfkWztPOJNyjj6RR9d8H4hH2pDISHztmYaxjDcKVvTikx8OiZaJAPSc+xxezWNRM8bMAx1I8pDZfNX1jwJtLnZ3BCHKuxjbllLgLjOMtM3vBwq7tiapEDM5aiBfIC22OVx95JOTfFGVuLT3s6kJd4ZTBEttm3KIHoLU6HK8qIeiuLMQQbQ1WqntwUIlBqmJFmVYpsqz1M63sr/PGV6ySgkVKFL5Ql4g22mnD8acWchBuo6WpgzpeVGJuUg6J3b4qL97axuVRESX70DdzHto14ytaPRIyKusbxFQlubpRV3gUhdWsxCt6GEYpyxBp342T9gRxhy9vXHkyghcKnzJKpCViHbURrC5HZ20GHtDH4u/6MOG9Ndct2VzExq9Yuo+M9SP6JHymLhDnXWIcSXBQoOepz+xV8t7GyuKdeD81WKqzpsAtIeJGuaKt1AF56U05jfwjtjPiDJ3iIwFmwm0wspx52Gk/gumOFUTOONxiBOA6CsrYKbiW7NU1RBIrl8v+eYlkr4hJ439zWRJ/n3/OjgPvV1xLn+PP36uPwhjnQkTaonCWSCjTKrzqNy9dPMh4T2gpLeGdLCOlOmsqTAlhMp6K64fdJjfKsfBo4XW29jq75iSE3Wm2M2VGFaxGMXY5ohFh24/FNVsxoyYCQbZw9LF/jQ8ck2nJGYn7tYFky/qjgzICXZVx+I8jFH1sszHMtggTbcsw17YRWxzHkaBZUWJUiJ09Tn2Ivou/7F6YiOhqC2bmLkcHshe+RuDewpQQfp1R77TpiKtJc3Xp0gjTzLamkgzoOYqQufQoW81HqmpFvJaB01oyOQwJ2G2cRZQRI5yHE7oFMVoq/Txd3EefruYiRy0UM1oEdqJdz8KDzK7ZcYYM97z8dfgsbZpIIXm6nbopMCWE34TwTcEG5DoKXd1rvPBM4gOkfJ3E0cpYnKYH5FcDNUVqlXoesq/zP2uscH/5pSwRxVEYRl7Uq7REXaz3314IKSG8XPGV2Psrzojb/X0VMbJoKeMTWFnkjZytTsPO8lNYVLhJvHhyjPVbYRRTbVbw25351mez3baWFnbvcx1FYuDwBfpvJwWKt5fK9HQxICWkDU1JfiNynsmBHZnwWsxXSDARFVo1ja50rDu3C+Oyv8G7knMQfAXUgPTp+L5wMxGWIjKk/Hk2nLxEufP/m1N4ILC94qQpz4pTlQmYnbsK73v5eonmhpQQ3j+fbHW/f15fMuxWcWtzGI0qrq54wTIcTyQMxEPxfaUBE0e0fOfgYwkDxGX/QVmLsLboJ0TTcsZnuJmYlpAiGnTHaTasIG9yTNYC/Iv60j6+X7O+39YXSAkRFweUHhQjRia8xHCN6oGKU6Jea0T2YnxCo70LjSo2enwjnS+B0v308DwIXidlfJA6Bb0yvsbsvDXYXLKf7E2CONPdHARxKScvo/zWm43n9mJ67kr0ob/1HsUTryaNoj4MvGRE1KIBIfcQ3k+djBxHwc9KYGLYuJ+hB9lOUfs88sP5mtOe6TNEWf2Dcb3E5+q31VjcS7OnIxHbjRTVP3MuxpOryX9zOdmdjUQSV3EcJPt2ishiw5tOs9NK/c0mpNiyxFsITlTG0YA5g530u/wZ/uy8wh8RTMsRv4Low5SJ6ECDoCUMtS9oQEh7WmJYCbxsMAkWesA99GDfFW0ne7AEHyVP8LqCornAs41f8fBCQgDeoQHwKRE1IGMWgsg+hRJZCwrWY2nhViyhZefr/LWYnPM9RpJn1D8jTJTf8Gf4sy1pnBuLBoR0TR6D6TnLsINmAtuDT1Im4SnJWbgruDhoQAgvP1w6yi9EfIRmApc+NjWlfAXeQ2pDWtf73hW0HBoQcgWXFlcI8TNcIcSv0AP/DzII4GBXfouiAAAAAElFTkSuQmCC" alt="Clienapp Whatsapp"/>
      Hablar por Whatsapp</span> 
    <span class="converse-menu-icon">X</span>  
  </div> 
  <div class="converse-chatbox" style="height: 100%; display: block;">    
    <div class="converse-conversation">
    </div>
    <div class="clienchat-link">
      <a href='https://www.clienapp.com' target='_blank'>CLIENAPP.COM</a>
    </div>
  </div>
</div>
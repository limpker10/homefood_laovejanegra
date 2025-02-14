
const KEY_LENGHT= 3;

import router from './../router/router';

const UTILS = {
    chrypter: {
        encode(val) {
          let key = UTILS.str.rand(KEY_LENGHT)
          return window.base64.encode(val+key)
        },
        decode(val) {
         
          let str = window.base64.decode(val)
          return str.substring(0,str.length-KEY_LENGHT);
        },
        
    },
    str:{
      rand(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
           result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
      }
    },
    route:{
      redirect(id=null){
        router.push('/posSystem/'+id)
      },
    },
    toastr:{
      notificacion(message,vm){
        vm.$swal({
          toast: true,
          position: "top-end",
          icon: "success",
          title: message,
          showConfirmButton: false,
          timerProgressBar: true,
          timer: 2500,
        });
      },
      success(message,vm){
        vm.$swal({
          toast: true,
          position: "top-end",
          icon: "success",
          title: message,
          showConfirmButton: false,
          timerProgressBar: true,
          timer: 2500,
        });
      },
      error(message,vm){
        vm.$swal({
          icon: "error",
          title: "Oops...",
          text: message,
        });
      }
    },
    rules :{
      only_numbers : 
        value => {
          const pattern = /^\d+$/
          return pattern.test(value) || 'Este campo solo admite numeros'
        }
      ,
      required: [
        value =>
        !!value || "Campo es requerido",
      ],
      fail: [
        value =>
        false || "El campo es invalido",
      ]
      

    },
    const:{
      TIMEOUT:1000,
      REFRESH:100,
    }

}
export default UTILS;
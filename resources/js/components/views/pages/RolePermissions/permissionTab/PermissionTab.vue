<template>
    <div class="my-2">
       <v-form ref="form" v-model="valid">
            <create-permission-card
              :name.sync="permission.name"
              :description.sync="permission.description"
              :loading="loading"
              :validate="validate"
              :disabled="created"
            >
            </create-permission-card>
        </v-form>
        <br>
        <list-permission-card
            :headers.sync="headers"
            :permissions_list.sync="permissions_list"
            :loading="loading"
            :validate="reload"
            :disabled="created"
            :options="options"
        >
        </list-permission-card>

    </div>
</template>

<script>
import CreateCard from "./components/CreateCard";
import ListCard from "./components/ListCard";
export default {
  components: {
       'create-permission-card': CreateCard,
       'list-permission-card': ListCard,
  },
  data() {
    return {
        user: {
        },
        nameRules: [
            (v) => !!v || 'Nombre es obligatorio'
        ],
        tab: null,
        menu: false,
        text: '',
        timeout: 2000,
        valid: false,
        loading: false,
        isLoadingModal:false,
        edit_loading:false,
        delete_loading:false,
        table_loading:false,
        permissions_list:[],
        permission:{},
        options: {},
        headers: [
            { text: "Permiso", value: "name" },
            { text: "Descripción", value: "description" },
            { text: "Acciones", sortable: false, value: "action" },
        ],
        
        editedIndex: -1,
        editedItem: {
            name: '',
            description: '',
        },
        dialog: false,
        deleteDialog: false,
        created:false
    }
  },
  created() {
      this.valid = false;
      this.getpermissions();
  },
  methods: {
    parseArray(obj) {
        return Object.keys(obj).map((key) => [Number(key), obj[key]]);
    },
    scrollToEl(el) {
        let vm = this;
        let myEl = document.getElementById("input-" + el);
        vm.$smoothScroll({
            scrollTo: myEl,
            offset: -150,
            updateHistory: false,
        });
    },
    scrollToWrongField() {
        let vm = this;
        let error_bag = vm.$refs.form.errorBag;
        let fields = vm.parseArray(error_bag);
        // 1 : validation result \ 0: Element id
        for (let el of fields) {
            if (el[1]) {
            vm.scrollToEl(el[0]);
            break;
            }
        }
    },
    validate() {
        let vm = this;
        if (vm.$refs.form.validate()) {
            // Continue
            vm.createPermission();
        } else {
            vm.scrollToWrongField();
        }
    },
    
    reload(){
        this.getpermissions();
    },

    async createPermission(){
        try{
            const permission = await API.permissions.create(this.permission);
            this.getpermissions();
            this.loading = false;
            this.$refs.form.reset();
            this.$swal({
                toast: true,
                position: 'top-end',
                icon:'success',
                title:'Permiso creado correctamente',
                showConfirmButton:false,
                timerProgressBar:true,
                timer:2500
            });
        }catch(error){
            this.$swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Error al actualizar.'
            });
            console.error(error.response.status);
        }
    },
    async getpermissions( ){
        try{
            const permissions = await API.permissions.list();
            this.permissions_list = permissions.data;
        }catch(error){
            console.error(error.response.status);
        }
    },

  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
  .img-avatar{
    background-color: #FFD166 !important;
    border-radius: 50px !important;
  }
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
  .mt-2{
    text-align: right;
  }
</style>

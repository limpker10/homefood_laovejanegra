<template>
    <div class="my-2">
        <v-form ref="form" v-model="valid">
            <create-rol-card
              :title.sync="role.title"
              :name.sync="role.name"
              :loading="loading"
              :validate="validate"
              :disabled="created"
            >
            </create-rol-card>
        </v-form>
        <br>
        <list-rol-card
            :headers_role.sync="headers_role"
            :role_list.sync="role_list"
            :loading="loading"
            :validate="reload"
            :disabled="created"
            :options="options"
        >
        </list-rol-card>
        <br>
        <assign-permission-card
            :loading="loading"
            :role_list.sync="role_list"
            :headers.sync="headers"
            :role_selected.sync="role_selected"
            :permissions_list.sync="permissions_list"
            :validate="saveChanges"
            :switch_model.sync="switch_model"
        >
        </assign-permission-card>
       
    </div>
</template>

<script>
import CreateCard from "./components/CreateCard";
import ListCard from "./components/ListCard";
import AssignPermissionCard from "./components/AssignPermission";
export default { 
    components: {
       'create-rol-card': CreateCard,
       'list-rol-card': ListCard,
       'assign-permission-card': AssignPermissionCard
    },
    data() {
        return {
            promocode: {},
            switch_model: [],
            role_selected:null,
            nameRules: [
                (v) => !!v || 'Nombre es obligatorio'
            ],
            titleRules: [
                (v) => !!v || 'Título es obligatorio'
            ],
            valid: false,
            role: {
                name:'',
                title:''
            },
            tab: null,
            date: '1990-10-09',
            menu: false,
            text: '',
            timeout: 2000,
            gender: 'male',
            valid: false,
            loading: false,
            role_list:[],
            permissions_list:[],
            options: {},
            headers: [
                { text: "Permiso", value: "name" },
                { text: "Descripción", value: "description" },
                { text: "Asignar", sortable: false, value: "action" },
            ],

            headers_role: [
                { text: "Nombre", value: "title" },
                { text: "Identificador", value: "name" },
                { text: "Acciones", sortable: false, value: "action" },
            ],
            
            edit_loading:false,
            delete_loading:false,
            isLoadingModal:false,
            itemsperpage: 5,
            created:false,
        }
    },
    created() {
        this.valid = false;
        this.getroles();
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
                vm.createRol();
            } else {
                vm.scrollToWrongField();
            }
        },
        reload(){
            this.getpermissions();
            this.getroles();
        },
        save (date) {
            this.$refs.menu.save(date)
        },

        async createRol(){
            try{
                const role = await API.roles.create(this.role);
                this.getroles();
                this.created = true;
                this.$refs.form.reset();
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Rol creado correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            }catch(error){
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error al crear.'
                });
                console.error(error);
            }
        },
        
        async getroles( ){
            try{
                const role = await API.roles.list();
                this.role_list = role.data;
            }catch(error){
                console.error(error);
            }
        },
        
        async getpermissions( ){
            try{
                const permissions = await API.permissions.list();
                this.permissions_list = permissions.data;
            }catch(error){
                console.error(error);
            }
        },
        async saveChanges(){
            try{
                const permissions = await API.roles.assignpermissions(this.role_selected,{permissions:this.switch_model});
                this.loading = false;
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Permisos asignados correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            }catch(error){
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error al asignar.'
                });
                console.error(error);
            }
        },
        
    }
}
</script>

<style lang="scss" scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease;
}
.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter,
.slide-fade-leave-to {
  transform: translateX(10px);
  opacity: 0;
}
.btn-actions {
  background-color: #fff;
  color: #121212;
}
.v-input__slot {
  background: red;
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

<template>
    <div>

        <v-dialog
               v-model="abierto"
               max-width="650px"
               max-height="500px"
               :persistent="true"
          >
            <v-card  max-width="640px">
                <div v-if="dialog=='search'">
                    <v-card-title primary-title>
                        <p class="mb-0">Buscar Cliente</p>
                    </v-card-title>
                    
                    <v-row dense class="px-5 py-1 align-center">
                        <v-col cols="7">
                            <v-text-field
                            v-model="searchQuery"
                            append-icon="mdi-magnify"
                            class="flex-grow-1 mr-1"
                            single-line
                            hide-details
                            placeholder="Buscar por nombre …"
                            @keyup.enter="searchUser(searchQuery)"
                            @keyup.delete="researchUser(searchQuery)"
                            ></v-text-field>
                        </v-col>
                        <v-spacer></v-spacer>
                    </v-row>
                    <v-data-table
                            :headers="headers"
                            :items="items"
                            :options.sync="opciones"
                            :items-per-page="itemsperpage"
                            :server-items-length="total"
                            :footer-props="{
                                itemsPerPageOptions: [5, 10],
                                itemsPerPageText: 'Filas por pagina',
                                itemsPerPageAllText: 'Todo',
                            }"
                            @item-selected="seleccionarFila"
                            @click:row="clickearFila"
                            :item-key="itemKey"
                            :show-select="showSelect"
                            class="elevation-0"
                            loading-text="Cargando... Por favor espere"
                            max-height="400px"
                            :single-select="singleSelect"
                        >

                    </v-data-table>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn @click="close">Cancelar</v-btn>
                        <v-btn @click="selectCustomer" color="primary">Seleccionar</v-btn>
                    </v-card-actions>
                </div>

                <div v-else>
                     <v-card-title primary-title>
                        <p>Crear Cliente</p>
                    </v-card-title>
                    
                    <v-container class="px-5">
                        <v-form v-model="formulario" ref="form" lazy-validation>
                            <template>
                                <v-row>
                                    <v-col cols="6">
                                            <v-select
                                                v-model="formCreate.id_tipo_doc"
                                                :items="docs"
                                                :disabled="cargando"
                                                item-text="tipo_documento"
                                                item-value="id_tipo_doc"
                                                label="Tipo de documento"
                                                :rules="required"
                                            />
                                    </v-col>
                                    <v-col cols="6">
                                            <v-text-field
                                                v-model="formCreate.nro_doc"
                                                label="Nro. documento"
                                                type="number"
                                                :disabled="cargando"
                                                :rules="required"
                                            >
                                                <template v-slot:append>
                                                    <v-icon
                                                        color="primary"
                                                        class="mr-2"
                                                        @click="obtenerDataDocumentos(formCreate.nro_doc.tipo_documento)"
                                                        :disabled="cargando || busquedaDesactivada"
                                                    >
                                                        mdi-magnify
                                                    </v-icon>
                                                </template>
                                            </v-text-field>
                                    </v-col>
                                    <v-col cols="6">
                                            <v-text-field
                                                v-model="formCreate.nombre"
                                                label="Nombre"
                                                :disabled="cargando"
                                                type="text"
                                                autocomplete="name"
                                                :rules="required"
                                            />
                                    </v-col>
                                    <v-col cols="6">
                                            <v-text-field
                                                v-model="formCreate.direccion"
                                                label="Dirección"
                                                :disabled="cargando"
                                                type="text"
                                                autocomplete="address"
                                                :rules="required"
                                            />
                                    </v-col>
                                    <v-col cols="6">
                                            <v-text-field
                                                v-model="formCreate.email"
                                                label="Correo Electrónico"
                                                :disabled="cargando"
                                                type="text"
                                                autocomplete="email"
                                            />
                                    </v-col>
                                    <v-col cols="6">
                                            <v-text-field
                                                v-model="formCreate.telefono"
                                                label="Telefono"
                                                :disabled="cargando"
                                                type="number"
                                                autocomplete="tel"
                                            />
                                    </v-col>
                                </v-row>
                            </template>
                            
                        </v-form>
                    </v-container>
                    <v-card-actions>
                            <v-spacer />
                            <v-btn @click="close">Cancelar</v-btn>
                            <v-btn @click="createCustomer" color="primary">Crear</v-btn>
                    </v-card-actions>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
export default {
    searchQuery: '',
    props: {
        cargando: { type: Boolean },
        abierto: { type: Boolean },
        dialog: { type: String },
        customer: { type: Object }
    },
    data() {
        return {
            headers: [
                { text: 'ID', value: 'id_cliente', sortable: false },
                { text: 'Nombre', value: 'nombre', sortable: false },
                { text: 'Correo Electrónico', value: 'email', sortable: false },
                { text: 'Nro. Documento', value: 'nro_doc', sortable: false },
            ],
            opciones: { itemsPerPage: 5 },
            total: 0,
            items: [],
            itemKey: 'id_cliente',
            singleSelect: true,
            showSelect: true,
            docs: [],
            slectedCustomer: {},

            formCreate:{
                doc:'',
                nombre: '',
                direccion: ''
            },
            doc:'',
            formulario:true,
            busquedaDesactivada: false,
            
            datosBus: {
                tipoDoc: '',
                numDoc: ''
            },
            required: [
                v => !!v || 'Campo es requerido',
            ],
            itemsperpage: 5,
            searchData: {},
        }
    },
    created(){
        //this.getCustomer();
        this.getTiposDocumento();
    },
    computed:{
        $customer: {
            get() {
                return this.customer;
            },
            set(value) {
                this.set_prop("customer", value);
            },
        },
        $abierto: {
            get() {
                return this.abierto;
            },
            set(value) {
                this.set_prop("abierto", value);
            },
        },
    },
    watch:{
        opciones(event) {
            this.getCustomer(event.page, event.itemsPerPage);
        },
    //     options(event) {
    //   this.itemsperpage=event.itemsPerPage;
    //   this.getlist(event.page, event.itemsPerPage);
    // },


    },
    methods:{
        async getCustomer(page=1, perpage=5){
            try{
                this.searchData = {
                    perpage: perpage,
                    data: this.searchQuery,
                };
                const data = await API.customer.list(page,this.searchData);
                console.log(data.data.data)
                this.items = data.data.data;
                this.total = data.data.total;
            }catch(error){
                console.error(error);
            }
        },
        async createCustomer(){
            if(this.$refs.form.validate()){
                try{
                    const data = await API.customer.create(this.formCreate);
                    //this.set_prop("customer", data.data);
                    this.slectedCustomer = data.data;
                    this.selectCustomer();
                    this.set_prop("abierto", false);
                    this.$refs.form.reset();
                }catch(error){
                    this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon:'error',
                        title:'Cliente/DNI ya registrado',
                        showConfirmButton:false,
                        timerProgressBar:true,
                        timer:2500
                    });
                    console.error(error);
                }
            }
            else{
                return;
            }
        },
        async getTiposDocumento(){
            try{
                const data = await API.services.tipoDocumentos();
                this.docs = data.data;
            }catch(error){
                console.error(error);
            }
            
        },
        close(){
            this.set_prop("abierto", false);
            this.$refs.form.reset();
        },
        seleccionarFila(row) {
            console.log(row);
            this.slectedCustomer=row.item;
        },
        clickearFila(row, other) {
            console.log(row);
            other.select(!other.isSelected);
            this.slectedCustomer=row;
        },
        set_prop(_prop, value) {
            this.$emit("update:" + _prop, value);
        },

        selectCustomer(){
            this.set_prop("customer", this.slectedCustomer);
            this.set_prop("abierto", false);
        },

        
        /*cambiarDocType( id_tipo_doc ) {
            this.formCreate.id_tipo_doc = id_tipo_doc
        },*/

        async obtenerDataDocumentos(doc) {
            try {
                this.$store.commit('LOADER',true);

                this.datosBus.tipoDoc = this.formCreate.id_tipo_doc;
                this.datosBus.numDoc = this.formCreate.nro_doc;
                const rpta = await axios.post("/api/buscarDniRuc",this.datosBus);
                if(rpta.data.success === true){
                    if(this.datosBus.tipoDoc === 1){
                        this.formCreate.nombre = rpta.data.nombre_o_razon_social;
                        this.formCreate.direccion = rpta.data.direccion_completa;
                        this.$store.commit('LOADER',false);
                    }else if(this.datosBus.tipoDoc === 2){
                        this.formCreate.nombre = rpta.data.result.Nombres + " " + rpta.data.result.Apellidos;
                        this.$store.commit('LOADER',false);
                    }
                    console.log(this.formCreate);
                }else{
                    this.$store.commit('LOADER',false);
                }

                this.$store.commit('LOADER',false);

            } catch (error) {
                this.$store.commit('LOADER',false);
            }
        },

        
        searchUser() {
            this.items= [];
            this.getCustomer(1,this.itemsperpage);
        },

        researchUser(){
            if(this.searchQuery.length==0){
                this.searchQuery = '';
                this.getlist(1,this.itemsperpage);
            }
        },
    }
}
</script>
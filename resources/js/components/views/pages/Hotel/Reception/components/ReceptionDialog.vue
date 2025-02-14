<template>
    <div>

        <v-dialog
               v-model="abierto"
               max-width="600px"
               max-height="500px"
               :persistent="true"
          >
            <v-card>
                <div v-if="dialog=='search'">
                    <v-card-title primary-title>
                        <p>Reservas del dia</p>
                    </v-card-title>
                    <v-data-table
                            :headers="headers"
                            :items="items"
                            :options.sync="opciones"
                            :items-per-page="5"
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
                                            />
                                    </v-col>
                                    <v-col cols="6">
                                            <v-text-field
                                                v-model="formCreate.nro_doc"
                                                label="Nro. documento"
                                                type="number"
                                                :disabled="cargando"
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
                                            />
                                    </v-col>
                                    <v-col cols="6">
                                            <v-text-field
                                                v-model="formCreate.direccion"
                                                label="Dirección"
                                                :disabled="cargando"
                                                type="text"
                                                autocomplete="address"
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

            },
            doc:'',
            formulario:true,
            busquedaDesactivada: false,
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
        opciones: {
            handler() {
                console.log(this.opciones);
                this.getCustomer();
            },
        },

    },
    methods:{
        async getCustomer(){
            try{
                const data = await API.customer.list('1',{ perpage: 5 });
                console.log(data.data.data)
                this.items = data.data.data;
                this.total = data.data.total;
            }catch(error){
                console.error(error);
            }
        },
        async createCustomer(){
            try{
                const data = await API.customer.create(this.formCreate);
                this.set_prop("customer", data.data);
                this.set_prop("abierto", false);
            }catch(error){
                console.error(error);
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
            console.log(this.slectedCustomer)
            this.set_prop("customer", this.slectedCustomer);
            this.set_prop("abierto", false);
        },

        obtenerDataDocumentos(doc) {
            
        },
    }
}
</script>
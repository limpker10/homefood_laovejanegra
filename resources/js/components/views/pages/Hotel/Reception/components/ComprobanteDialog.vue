<template>
    <div>
        <v-dialog v-model="abierto" max-width="800px" persistent>
            <v-card>
                <v-card-title class="subtitle-1">Procesar Comprobante</v-card-title>
                <v-card-text>
                    <v-row dense>
                        <v-col cols="6" >
                            <v-select
                                :items="tipo_comprobante"
                                label="Comprobante"
                                placeholder="Selecciona un tipo de comprobante"
                                item-text="tipo_comprobante"
                                item-value="id_tipo_comprobante"
                                v-model="generate_sales.header.id_tipo_comprobante"
                                hide-details="auto"
                                @change="getserie"
                            ></v-select>
                        </v-col>
                        <v-col cols="6" >
                            <v-select
                                :items="serie"
                                label="Serie"
                                item-text="serie"
                                item-value="id_serie"
                                v-model="generate_sales.header.id_serie"
                                hide-details="auto"
                            ></v-select>
                        </v-col>
                    </v-row>

                    <v-text-field
                        label="Cliente"
                        hide-details="auto"
                        readonly
                        v-model="generate_sales.header.nombre_cliente"
                    ></v-text-field>

                    <v-simple-table>
                        <template v-slot:default>
                        <thead>
                            <tr>
                                <th class="text-left"> Producto </th>
                                <th class="text-left"> Cantidad </th>
                                <th class="text-left"> P.U </th>
                                <th class="text-left"> P.T </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>item.producto.nombre_producto}}</td>
                                <td>item.cantidad}}</td>
                                <td>item.precio}}</td>
                                <td>item.total}}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align:right;"><b>Subtotal: </b></td>
                                <td colspan="1">tabs_form[tab].orden.subtotal}}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align:right;"><b>IGV (18%): </b></td>
                                <td colspan="1">tabs_form[tab].orden.igv}}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align:right;"><b>Total: </b></td>
                                <td colspan="1">tabs_form[tab].orden.total}}</td>
                            </tr> 
                        </tfoot>
                        </template>
                    </v-simple-table>
                    <v-select
                        :items="metodospago"
                        label="Metodo de Pago"
                        placeholder="Selecciona un método de pago"
                        item-text="medio_pago"
                        item-value="id_medio_pago"
                        v-model="generate_sales.header.id_medio_pago"
                        hide-details="auto"
                    ></v-select>
                    <v-select
                        :items="monedas"
                        label="Moneda"
                        placeholder="Seleccione moneda"
                        item-text="nombre"
                        item-value="id_moneda"
                        v-model="generate_sales.header.id_moneda"
                        hide-details="auto"
                    ></v-select>
                    <v-text-field
                        v-if="generate_sales.header.id_medio_pago==3"
                        label="Nro. Operación"
                        hide-details="auto"
                        v-model="generate_sales.header.nro_operacion"
                    ></v-text-field>
                    <v-textarea
                        name="input-7-1"
                        rows="2"
                        label="Comentario"
                        v-model="generate_sales.header.comentario"
                        hide-details="auto"
                    ></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" dark @click="close"
                    >Cancelar</v-btn
                    >
                    <v-btn color="primary" @click="saveComprobante"
                    >Guardar</v-btn
                    >
                </v-card-actions>
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
        customer: { type: Object },
        generate_sales: { type: Object },
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
            
            metodospago: [],
            monedas: [],
            tipo_comprobante: [],
            serie: [],

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

        
        /*cambiarDocType( id_tipo_doc ) {
            this.formCreate.id_tipo_doc = id_tipo_doc
        },*/

        obtenerDataDocumentos(doc) {
            
        },

        async getMetodoPago(){
            try{
                const data = await API.services.metodoPago();
                this.metodospago = data.data;
            }catch(error){
                console.error(error);
            }
        },
        async getMoneda(){
            try{
                const data = await API.services.moneda();
                this.monedas = data.data;
            }catch(error){
                console.error(error);
            }
        },
        async getTiposComprobante(){
            try{
                const data = await API.services.tipoComprobante();
                this.tipo_comprobante = data.data;
            }catch(error){
                console.error(error);
            }
        },

        async getserie(){
            try{
                const data = await API.services.serie(this.generate_sales.header.id_tipo_comprobante);
                this.serie = data.data;
            }catch(error){
                console.error(error);
            }
        },


        async saveComprobante (){

        }
    }
}
</script>
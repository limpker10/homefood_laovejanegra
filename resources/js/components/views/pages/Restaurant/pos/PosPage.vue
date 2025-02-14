<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">{{mesa.zona.nombre}} - {{mesa.nro_mesa}} | Mozo: {{ nombre_mozo }}</h2>
      </div>
      <v-spacer></v-spacer>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on" :to="'/tablesSystem/'+mesa.id_sucursal"> 
            <v-icon>mdi-store-outline</v-icon>
            </v-btn>
        </template>
        Volver a Mesas
        </v-tooltip>
    </div>
    <div class="my-2">
        <div>
            <v-row no-gutters >
                <v-col cols="12"
                style="padding: 10px;">
                <v-card>
                    <v-sheet
                        class="mx-auto"
                        max-width="1300"
                    >
                        <v-slide-group
                        show-arrows
                        >
                        <v-slide-item
                            v-for="(n,index) in categorias"
                            :key="index"
                            v-slot="{ active, toggle }"
                        >
                            <v-card
                            class="ma-1"
                            :color="active ? 'black' : n.color"
                            height="50"
                            width="130"
                            @click="toggle(); getProductos(n.id_categoria_sucursal)"
                            >
                                <v-card-title class="white--text subtitle-2" v-text="n.categoria.nombre"></v-card-title>
                            </v-card>
                        </v-slide-item>
                        </v-slide-group>
                    </v-sheet>
                </v-card>
                </v-col> 

                <v-col cols="12" sm="7" md="7"
                style="padding: 10px;">
                    
                    <v-dialog v-model="observationDialog" max-width="300px">
                        <v-card>
                        <v-card-title class="subtitle-1">Comentario / Observaciones</v-card-title>
                        <v-card-text v-if="tabs_form.length>0">
                        <v-textarea  v-if="tabs_form[tab].orden.detalle.length>0"
                            name="input-7-1"
                            rows="2"
                            label="Comentario / Observación"
                            v-model="tabs_form[tab].orden.detalle[indxobservation].observaciones"
                        ></v-textarea>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="error" dark @click="observationDialog=false"
                            >Cancelar</v-btn
                            >
                            <v-btn color="primary" @click="updateObservation"
                            >Guardar</v-btn
                            >
                            <v-spacer></v-spacer>
                        </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-card min-height="450px">
                        <v-flex column py-0>
                            <v-layout align-center>

                                <v-flex style="width: 200px" py-0>
                                    <v-tabs v-model="tab" show-arrows>
                                        <v-tab v-for="(item,index) in tabs_form" :key="index">
                                            <v-icon>mdi-food-turkey</v-icon> Orden {{ index+1 }}
                                        </v-tab>
                                    </v-tabs>
                                </v-flex>

                                <v-flex shrink>
                                    <!--
                                    <v-btn @click="add" text>
                                        <v-icon color="primary">mdi-plus-box</v-icon>
                                    </v-btn>-->
                                </v-flex>
                                
                            </v-layout>

                            
                            <div v-if="tabs_form.length==0" style="text-align: center;height: 250px;align-items: center; padding: auto">
                                    
                                <v-container fill-height fluid>
                                    <v-row align="center" justify="center">
                                        <v-col cols="12" md="5" sm="12">
                                            <v-card
                                                class="pa-3"
                                                outlined
                                                v-if="isCashier === false"
                                            >
                                                <h2 style="margin:auto; color: rgba(0,0,0,.38);">Crear Nueva Orden</h2>
                                                <br>
                                                <!--<v-select
                                                    :items="mozos"
                                                    item-text="name"
                                                    item-value="id"
                                                    item-nombre="name"
                                                    v-model="id_mozo_sel"
                                                    label="Seleccione Mozo"
                                                    @input="crearOrdenMozo"
                                                    dense
                                                ></v-select>-->

                                                <div class="form-group mt-2">
                                                        <v-text-field
                                                            placeholder="Ingresa tu código"
                                                            v-model="value"
                                                            id="txtBox"
                                                            type="text"
                                                            readonly
                                                            outlined
                                                            hide-details
                                                        ></v-text-field>
                                                    </div>
                                                    <Keyboard @pressed="value = $event" 
                                                        @callback="validateCode(1)"
                                                        :selfValue="value"></Keyboard>
                                            </v-card>

                                            <v-card
                                                class="pa-3"
                                                outlined
                                                v-if="isCashier === true"
                                            >
                                                <h2 style="margin:auto;">No puede crear pedidos como cajero.</h2>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </div>
                            <v-tabs-items v-model="tab">
                                <v-tab-item
                                v-for="(form,index) in tabs_form"
                                :key="index"
                                >
                                <v-card flat style="height:100%;margin-top: 10px;">
                                    <v-row dense class="pa-2">
                                        <v-col cols="8">
                                            <br>
                                            <v-autocomplete
                                            v-model="form.orden.id_cliente"
                                            :items="customers"
                                            dense
                                            item-text="nombre"
                                            item-value="id_cliente"
                                            label="Elegir Cliente"
                                            placeholder="Elegir Cliente"
                                            prepend-icon="mdi-account-group"
                                            @change="updateCustomer(form.id_orden)"
                                            ></v-autocomplete>
                                        </v-col>
                                        <v-col cols="4" align="center">
                                            <v-btn @click="addNuevoCliente(form.id_orden)" medium color="primary">
                                                <v-icon small> mdi-plus</v-icon> Nuevo Cliente
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                    <v-divider></v-divider>
                                    <v-simple-table v-if="form.orden.detalle.length>0">
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th style="width:15%;" class="text-left"></th>
                                                    <th class="text-left">Producto</th>
                                                    <th style="width:25%;" class="text-left">CNT</th>
                                                    <th style="width:15%;" class="text-left">P.U (S/.)</th>
                                                    <th style="width:20%;" class="text-left">P.T (S/.)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                v-for="(item,index) in form.orden.detalle"
                                                :key="index"
                                                >
                                                    <td>
                                                        <v-btn @click="deleteItem(index)" icon :disabled="!validateAccess()">
                                                            <v-icon> mdi-delete</v-icon>
                                                        </v-btn>
                                                        <v-btn @click="editObservation(index)" icon>
                                                            <v-icon> mdi-comment-edit</v-icon>
                                                        </v-btn>
                                                    </td>
                                                    <td>{{ item.producto.nombre_producto }}</td>
                                                    <td> 
                                                        <v-row no-gutters>
                                                            <v-col
                                                                cols="12"
                                                                sm="4"
                                                            >
                                                                <v-btn @click="removeItem(index)"
                                                                    icon
                                                                    color="primary"
                                                                    :disabled="!validateAccess()"
                                                                    >
                                                                    <v-icon>mdi-minus-box</v-icon>
                                                                </v-btn>
                                                            </v-col>
                                                            <v-col
                                                                cols="12"
                                                                sm="4"
                                                            >
                                                                <v-text-field
                                                                    style="padding: 0px; margin: auto;"
                                                                    v-on:keyup="calcularTotalFila(index)"
                                                                    v-model="item.cantidad"
                                                                    hide-details="auto"
                                                                ></v-text-field>
                                                            </v-col>
                                                            <v-col
                                                                cols="12"
                                                                sm="4"
                                                            >
                                                                <v-btn @click="addItem(index)"
                                                                    icon
                                                                    color="primary"
                                                                    >
                                                                    <v-icon>mdi-plus-box</v-icon>
                                                                </v-btn>
                                                            </v-col>
                                                        </v-row>
                                                    </td>
                                                    <td>
                                                        <v-text-field style="padding: 0px; margin: auto;"
                                                            v-on:keyup="calcularTotalFila(index)"
                                                            v-model="item.precio"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                    <td>
                                                        <v-text-field style="padding: 0px; margin: auto;"
                                                            v-model="item.total"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" style="text-align:right;"><b>Subtotal: </b></td>
                                                    <td colspan="1">
                                                        <v-text-field
                                                            v-model="form.orden.subtotal"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" style="text-align:right;"><b>IGV (10%): </b></td>
                                                    <td colspan="1">
                                                        <v-text-field
                                                            v-model="form.orden.igv"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" style="text-align:right;"><b>Total: </b></td>
                                                    <td colspan="1">
                                                        <v-text-field
                                                            v-model="form.orden.total"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </template>
                                    </v-simple-table>
                                    <div v-else style="text-align: center;height: 250px;display: flex;align-items: center;">
                                        <h2 style="margin:auto; color: rgba(0,0,0,.38);">Productos no seleccionados</h2>
                                    </div>
                                    
                                    <v-divider></v-divider>
                                    <v-card-actions class="justify-end" >
                                        <v-btn color="error" icon @click="deleteMesaOrden(form.orden.id_orden)" :disabled="!validateAccess()">
                                        <v-icon>mdi-delete-outline</v-icon>
                                        </v-btn> 
                                        <v-spacer></v-spacer>
                                        <v-btn small color="error" :disabled="!validateAccess()" @click="deleteMesaOrden(form.orden.id_orden)" v-if="form.orden.detalle.length>0">
                                        <v-icon>mdi-close</v-icon> Cancelar
                                        </v-btn>
                                        <v-btn small color="info" v-if="form.orden.detalle.length>0" @click="printCuenta(form.orden.id_orden)">
                                        <v-icon>mdi-printer-pos</v-icon> Imprimir Cuenta
                                        </v-btn>
                                        <v-btn small color="accent" v-if="form.orden.detalle.length>0"  @click="printCocina(0)">
                                        <v-icon>mdi-chef-hat</v-icon> Enviar Cocina</v-btn>
                                        <v-btn small color="accent" v-if="form.orden.detalle.length>0"  @click="printCocina(1)">
                                        <v-icon>mdi-glass-cocktail</v-icon> Enviar Barra</v-btn>
                                        <v-btn small color="primary" v-if="form.orden.detalle.length>0" @click="preLoadData">
                                        <v-icon>mdi-printer-pos</v-icon> Procesar Orden
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                                </v-tab-item>
                            </v-tabs-items>
                        </v-flex>
                    </v-card>
                </v-col>

                <!--ITEMS-->
                <v-col  cols="12" sm="5" md="5"
                style="padding: 10px;">
                    
                    <v-card style="height:500px; overflow-y: auto;">
                        <!--<v-sheet
                            class="mx-auto"
                            max-width="700"
                        >
                            <v-slide-group
                            show-arrows
                            >
                            <v-slide-item
                                v-for="(n,index) in categorias"
                                :key="index"
                                v-slot="{ active, toggle }"
                            >
                                <v-card
                                class="ma-1"
                                :color="active ? 'black' : n.color"
                                height="50"
                                width="120"
                                @click="toggle(); getProductos(n.id_categoria_sucursal)"
                                >
                                
                                    <v-card-title class="white--text subtitle-2" v-text="n.categoria.nombre"></v-card-title>

                                </v-card>
                            </v-slide-item>
                            </v-slide-group>
                        </v-sheet>-->

                        <v-divider></v-divider>
                        <search-bar @changeInput="addItemOrder" value="changeInput"></search-bar>
                        <v-container fluid>
                            <v-row dense>
                            <v-col
                                v-for="(card,index) in productos"
                                :key="index"
                                cols="4"
                            >
                                <v-card @click="addItemOrder(card)">
                                    <v-img
                                        :src="'../storage/files/uploads/'+card.path+'/'+card.filename"
                                        class="white--text align-end"
                                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.8)"
                                        height="150px"
                                    >
                                        <v-card-title class="subtitle-2" v-text="card.nombre_producto"></v-card-title>
                                    </v-img>
                                </v-card>
                            </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-col>
            </v-row>


            <v-dialog v-model="verComprobanteDialog" max-width="500px">
                <v-card>
                    <iframe :src="urlComprobante" frameborder="0" height="700px" width="100%">
                    </iframe>
                </v-card>
            </v-dialog>
            
            <v-dialog v-model="comprobanteDialog" max-width="500px" persistent>
                <v-card>
                    <v-card-title class="subtitle-1">Procesar Venta</v-card-title>
                    <v-card-text>
                        <v-form
                            ref="form"
                            v-model="valid2"
                            lazy-validation
                        >
                            <v-radio-group
                            v-model="row"
                            row
                            >
                                <v-radio
                                    label="Pago Ahora"
                                    value="radio1"
                                ></v-radio>
                                <v-radio
                                    label="Pago cuenta Habitación"
                                    value="radio2"
                                ></v-radio>
                            </v-radio-group>
                            <v-row dense v-if="row=='radio1'">
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
                                <v-col cols="6">
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
                            <v-row dense v-if="row=='radio2'">
                                <v-col cols="12">
                                    <v-select
                                        :items="habitaciones"
                                        @change="changeHabitacion"
                                        label="Habitación / Reserva"
                                        :item-text="getItemText"
                                        item-value="id_habitacion"
                                        hide-details="auto"
                                        v-model="habitacion"
                                        return-object
                                        :rules="requiredRules"
                                    ></v-select>
                                </v-col>
                            </v-row>
                            <!--<v-text-field
                                label="Cliente"
                                hide-details="auto"
                                readonly
                                v-model="generate_sales.header.nombre_cliente"
                            ></v-text-field>-->
                            <v-row dense>
                                <v-col cols="4" >
                                    <v-text-field
                                        label="Nro. Documento"
                                        hide-details="auto"
                                        v-model="generate_sales.header.nro_documento"
                                        append-icon="mdi-magnify"
                                        @click:append="toggleSearchCsutomer"
                                        :rules="[rules.required,rules.only_numbers]"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="8" >
                                    <v-text-field
                                        label="Cliente"
                                        hide-details="auto"
                                        v-model="generate_sales.header.nombre_cliente"
                                        :rules="requiredRules"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <v-text-field
                                label="Dirección"
                                hide-details="auto"
                                v-model="generate_sales.header.direccion_cliente"
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
                                    <tr v-for="(item,index) in  tabs_form[tab].orden.detalle" :key="index">
                                        <td>{{item.producto.nombre_producto}}</td>
                                        <td>{{item.cantidad}}</td>
                                        <td>{{item.precio}}</td>
                                        <td>{{item.total}}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="text-align:right;"><b>Subtotal: </b></td>
                                        <td colspan="1">{{tabs_form[tab].orden.subtotal}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align:right;"><b>IGV (10%): </b></td>
                                        <td colspan="1">{{tabs_form[tab].orden.igv}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align:right;"><b>Total: </b></td>
                                        <td colspan="1">{{tabs_form[tab].orden.total}}</td>
                                    </tr> 
                                    <tr>
                                        <td colspan="3" style="text-align:right;"><b>Total Dscto.: </b></td>
                                        <td colspan="1"><v-text-field
                                            v-model="generate_sales.header.total_dscto"
                                            type="number"
                                            v-on:keyup="calcularDscto(tab)"
                                        ></v-text-field></td>
                                    </tr> 
                                </tfoot>
                                </template>
                            </v-simple-table>

                            <v-container>
                                <v-row dense>
                                    <v-col cols="6" >
                                        <v-text-field
                                            label="Total Efectivo"
                                            hide-details="auto"
                                            v-model="generate_sales.header.total_efectivo"
                                            type="number"
                                            v-on:keyup="calcularVuelto(tab)"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="6" >
                                        <v-text-field
                                            label="Total Vuelto"
                                            hide-details="auto"
                                            v-model="generate_sales.header.total_vuelto"
                                            readonly
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-container>

                            <v-select
                                :items="metodospago"
                                label="Metodo de Pago"
                                placeholder="Selecciona un método de pago"
                                item-text="medio_pago"
                                item-value="id_medio_pago"
                                v-model="generate_sales.header.id_medio_pago"
                                hide-details="auto"
                                v-if="row=='radio1'"
                                :rules="requiredRules"
                            ></v-select>
                            <v-select
                                :items="monedas"
                                label="Moneda"
                                placeholder="Seleccione moneda"
                                item-text="nombre"
                                item-value="id_moneda"
                                v-model="generate_sales.header.id_moneda"
                                hide-details="auto"
                                v-if="row=='radio1'"
                                :rules="requiredRules"
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
                                v-if="row=='radio1'"
                            ></v-textarea>
                        </v-form>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="error" dark @click="close"
                        >Cancelar</v-btn
                        >
                        <v-btn color="primary" :loading="loading"  @click="saveComprobante"
                        >Guardar</v-btn
                        >
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialogDelete" max-width="400px">
            <v-card>
                <v-card-title class="subtitle-1">¿Desea cancelar esta Orden?</v-card-title>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="dialogDelete=false">Cancelar</v-btn>
                <v-btn color="primary" @click="deleteOrdenConfirm">Aceptar</v-btn>
                <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
            </v-dialog>

        <v-dialog v-model="nuevoClienteDialog" max-width="600px" >
          <v-card>
            <v-card-title>
              <span class="headline">Agregar Nuevo Cliente</span>
            </v-card-title>
            <v-card-text>
                <v-form
                    ref="formCliente"
                    v-model="valid"
                    lazy-validation
                >
                    <v-container>
                        <v-row>
                        <v-col cols="12" sm="6"  md="6" >
                            <v-select :items="tipos_doc" label="Tipo Documento" placeholder="Selecciona un tipo de Documento" v-model="nuevoCliente.id_tipo_doc" required item-text="tipo_documento" item-value="id_tipo_doc"></v-select>
                            <v-text-field  v-model="nuevoCliente.nombre" label="Nombre / Razón Social Cliente" placeholder="Nombre / Razón Social Cliente" :rules="requiredRules"
                            ></v-text-field>
                        <v-text-field v-model="nuevoCliente.email" label="Correo Electrónico" placeholder="Correo Electrónico"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6"  md="6" ><!--v-on:keyup.enter="buscarDoc()"-->
                            <v-text-field  v-model="nuevoCliente.nro_doc" label="Nro. Documento" placeholder="Nro. Documento" :rules="requiredRules" :append-outer-icon="iconSearch" @click:append-outer="SearchApi()"></v-text-field>
                            <v-text-field v-model="nuevoCliente.direccion" label="Dirección" placeholder="Dirección" :rules="requiredRules"
                            ></v-text-field>
                            <v-text-field v-model="nuevoCliente.telefono" label="Teléfono" placeholder="Teléfono"
                            ></v-text-field>
                        </v-col>
                        </v-row>
                    </v-container>
                </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" @click="closeCustomer" >Cancelar</v-btn>
              <v-btn color="primary" :disabled="!valid" @click="guardarCliente()"> Guardar </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        
            <v-dialog 
                v-model="dialogAccess" 
                persistent
                max-width="300px"
            >
                <v-card
                    class="pa-3"
                    outlined
                    
                >
                    <h2 style="margin:auto; color: rgba(0,0,0,.38);">Acceder a Orden</h2>
                    <br>
                    <div class="form-group mt-2">
                        <v-text-field
                            placeholder="Ingresa tu código"
                            v-model="value"
                            id="txtBox"
                            type="text"
                            readonly
                            outlined
                            hide-details
                        ></v-text-field>
                    </div>
                    <Keyboard @pressed="value = $event" 
                        @callback="validateCode(2)"
                        :selfValue="value"></Keyboard>
                    <v-spacer></v-spacer>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey" dark @click="goBack" >Cancelar</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </div>
  </div>
</template>
 
<script>
import print from 'print-js';
import Keyboard from './components/Keyboard.vue';
import SearchBar from './components/searchBar.vue';
export default {
  components: {
      Keyboard,
      SearchBar
  },
  data: () => ({
        value: '',
        validaccess: false,
        row: 'radio1',
        ordenLoading:false,
        tab:0,
        tabs: [1],
        active_tab: null,
        mesa:{zona:{nombre:''}},
        breadcrumbs: [{
                text: 'Mesas',
                disabled: true,
                //to: '/tablesSystem/'+this.
            },
            {
                text: 'Ordenes Mesa',
                disabled: true,
            }
        ],
        valid: true,
        valid2: true,
        form:{
            header:{ fecha_emision: new Date().toISOString().substr(0, 10), },
            detail:[]
        },
        tabs_form: [
        ],
        rules: {
          required: value => !!value || 'Campo Requerido.',
        },
        descriptionLimit: 60,
        entries: [],
        isLoading: false,
        search_provider: null,
        categorias: [
            {"id_categoria_sucursal":0,"id_categoria":0,"color":"#ffca40","tipo_producto_insumo":1,"categoria":{"id_categoria":0,"nombre":"TODOS","color":"#ffca40"}}
        ],
        productos: [],
        search_productos: null,
        get_producto: {
            unidad_medida: {}
        },
        entries_productos: [],


        descriptionLimit: 60,
        entries: [],
        isLoading: false,
        search_provider: null,
        get_customer: {},

        id_mesa:null,
        customers:[],
        observationDialog:false,
        indxobservation:0,

        comprobanteDialog:false,
        metodospago:[],
        monedas:[],
        tipo_comprobante:[],
        serie:[],

        generate_sales:{
            header:{
                total_vuelto: 0,
                total_dscto: 0,
            },
            detail:[]
        },

        verComprobanteDialog:false,
        urlComprobante:'',

        dialogDelete:false,
        deleteOrder:0,

        nuevoClienteDialog: false,
        tipos_doc: [],
        nuevoCliente: {
            id_tipo_doc: '',
            nombre:'',
            nro_doc:'',
            email: '',
            direccion: '',
            telefono: '',
        },
        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            v => !!v || 'Campo obligatorio',
            v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
        ],
        iconSearch:"mdi-magnify",
        datosBus: {
            tipoDoc: '',
            numDoc: ''
        },
        id_mozo_sel: null,
        mozos: [],
        nombre_mozo: '',
        loading: false,

        habitaciones: [{booking:{
            cliente: {
                nombre:''
            }
        }}],
        habitacion:{

        },
        rules: {
            required: (value) => !!value || 'Campo obligatorio',
            only_numbers: UTILS.rules.only_numbers,
            
        },
        dialogAccess: false,
        selectedOrden: null,
        isCashier: false,
  }),

 computed: {
    items() {
      return this.entries.map((entry) => {
        const Description = entry.nombre;
        return Object.assign({}, entry, { Description });
      });
    },

  },

  watch: {
    search_provider(val) {
      if (this.items.length > 0) return;
      if (this.isLoading) return;
      this.isLoading = true;
      fetch("/api/searchCustomer")
        .then((res) => res.json())
        .then((res) => {
          const { count, entries } = res;
          this.count = count;
          this.entries = entries;
        })
        .catch((err) => {
          console.log(err);
        })
        .finally(() => (this.isLoading = false));
    },
    get_customer(val){
        this.form.header.id_cliente = val.id_cliente;
        this.form.header.nombre = val.nombre;
        this.form.header.tipo_documento = val.tipo_doc.tipo_documento;
        this.form.header.nro_doc = val.nro_doc;
        this.form.header.direccion = val.direccion;
    },
  },


  created () {
    this.$store.commit('LOADER',true);
    this.id_mesa = this.$route.params.id;
    this.getUser();
    this.getHabitaciones();
    this.getMesa();
    this.getCustomers();
    this.getCategorias();
    this.getProductos(0);
    //this.getOrdenesMesa();
    this.getHabitaciones();
    this.getMetodoPago();
    this.getMoneda();
    this.getTiposComprobante();
    this.getTiposDoc();
    this.getMozos();
  },
  beforeMount(){
      this.getUserAndRoles();
  },
  methods: {
        getUserAndRoles(){
            let roles = localStorage.getItem("user_roles");
            //let user = localStorage.getItem("user_data");
            let user = JSON.parse(localStorage.getItem('user_data'));
            if(roles.includes("admin")){
                //this.crearOrdenMozo(user.id)
            }

            if(roles.includes("cajero")){
                this.isCashier = true;
            }
            
        },
        goBack(){
            return this.$router.go(-1)
        },
        getItemText(item) {
            let cliente = "";
            if(item.booking){ cliente = item.booking.cliente.nombre; }
            return `Habitacion: ${item.nombre_habitacion}, Cliente: ${cliente}`;//, Cliente: ${item.booking.cliente.nombre}`;
        },
        changeHabitacion(){
            this.generate_sales.header.nro_documento = this.habitacion.booking.cliente.nro_doc;
            this.generate_sales.header.nombre_cliente = this.habitacion.booking.cliente.nombre;
            this.generate_sales.header.direccion_cliente = this.habitacion.booking.cliente.direccion;
        },
        async getHabitaciones(id){
            try{
                const data = await API.rooms.active();
                this.habitaciones = data.data;
            }catch(error){
                console.error(error.response.status);
                this.dialogDelete = false;
            }
        },
        async validateCode(type){
            let loged = JSON.parse(localStorage.getItem("user_data"));
            await axios.post('/api/validateCodeAccess', { code: this.value, id: loged.id })
            .then(response => {
                const data = response.data;
                if(data.response == true){
                    this.validaccess = true;
                    if(type == 1){
                        console.log("entra aqui 1");
                        this.crearOrdenMozo(data.data.id);
                    }
                    if(type == 2){
                        console.log("entra aqui type 2");
                        if(this.tabs_form[0].orden.usuario.id == data.data.id){
                            this.dialogAccess = false;
                        }
                        else{
                            this.$swal({
                                icon:'error',
                                title:'Opps!',
                                text:'Código inválido 1',
                                timerProgressBar:true,
                                timer:2500
                            });
                        }
                    }
                }
                else{
                    this.validaccess = false;
                    this.$swal({
                        icon:'error',
                        title:'Opps!',
                        text:'Código inválido 2',
                        timerProgressBar:true,
                        timer:2500
                    });
                }
            }).catch((error)=>{
                console.log(error)
                this.$swal({
                    icon:'error',
                    title:'Opps!',
                    text:'Código inválido 3',
                    timerProgressBar:true,
                    timer:2500
                });
            });
        },
        getUser() {
            axios.get('/api/user').then(response => {
                this.currentUser = response.data;
           }).catch(error => {
                this.redirectLoginPage();
           }).finally(() => (this.isLoading = false));
        },
        validateAccess(){
          if(this.currentUser){
            let index = this.currentUser.roles.findIndex((element) => element != 'mozo')
            if(index != -1){
              this.validaccess = true;
              return true;
            }
            else{
              return false
            }
          }
        },
        async toggleSearchCsutomer(){
            try {
                this.$store.commit('LOADER',true);
                let tipodoc = 1;
                if(this.generate_sales.header.nro_documento.length==8){
                    tipodoc = 2;
                }
                else{
                    tipodoc = 1;
                }
                const rpta = await axios.post("/api/buscarDniRuc",{tipoDoc:tipodoc, numDoc:this.generate_sales.header.nro_documento});
                console.log(rpta)
                if(rpta.data.success === true){
                    if(tipodoc === 1){
                        this.generate_sales.header.nombre_cliente = rpta.data.nombre_o_razon_social;
                        this.generate_sales.header.direccion_cliente = rpta.data.direccion_completa;
                        this.generate_sales.header = JSON.parse(JSON.stringify(this.generate_sales.header))
                        this.$store.commit('LOADER',false);
                    }else if(tipodoc === 2){
                        this.generate_sales.header.nombre_cliente = rpta.data.result.Nombres + " " + rpta.data.result.Apellidos;
                        this.generate_sales.header = JSON.parse(JSON.stringify(this.generate_sales.header))
                        this.$store.commit('LOADER',false); 
                    }
                }else{
                    this.$store.commit('LOADER',false);
                    //this.setTextSnack("No encontrado","red");
                    this.$swal({ 
                        icon:'error',
                        title:'Opps!',
                        text:rpta.data.msg,
                        timerProgressBar:true,
                        timer:2500
                    });
                }
                console.log(rpta.data)
                this.$store.commit('LOADER',false);
            } catch (error) {
                this.$store.commit('LOADER',false);
            }
        },
        crearOrdenMozo(id_mozo){
            //let mozos = JSON.parse(localStorage.getItem("listamozos"))
            let mozos = this.mozos;
            console.log(mozos);
            let obj = mozos.filter(item => item.id === id_mozo);

            this.nombre_mozo = obj[0].name;
            this.id_mozo_sel = obj[0].id;
            this.add();
        },
        getMozos(){
                axios.get('/api/getMozos')
                .then((response) => {
                    this.mozos = response.data;
                    localStorage.setItem('listamozos', JSON.stringify(this.mozos));
                    this.getOrdenesMesa();
                })
                .catch( resonse => {
                    console.log(resonse);
                });
        },
        getMetodoPago(){
            let metodosPagoLocal = JSON.parse(localStorage.getItem('metodospago'));

            if(metodosPagoLocal == null){
                axios.get('/api/getMetodosPago')
                .then((response) => {
                    this.metodospago = response.data;
                    localStorage.setItem('metodospago', JSON.stringify(this.metodospago));
                })
                .catch( resonse => {
                    console.log(resonse);
                }) 
            }else{
                this.metodospago = metodosPagoLocal;
            }
            
        },
        getMoneda(){
            let monedasLocal = JSON.parse(localStorage.getItem('monedas'));

                axios.get('/api/getMoneda')
                .then((response) => {
                    this.monedas = response.data;
                    localStorage.setItem('monedas', JSON.stringify(this.monedas));
                })
                .catch( resonse => {
                    console.log(resonse);
                })
            
        },
        getTiposComprobante(){
            let tiposComprobanteLocal = JSON.parse(localStorage.getItem('tiposcomprobante'));

            if(tiposComprobanteLocal == null){
                axios.get('/api/getTiposComprobante')
                .then((response) => {
                    this.tipo_comprobante = response.data;
                    localStorage.setItem('tiposcomprobante', JSON.stringify(response.data));
                    this.$store.commit('LOADER',false);
                })
                .catch( resonse => {
                    console.log(resonse);
                    //this.$store.commit('LOADER',false);
                })
            }else{
                this.tipo_comprobante = tiposComprobanteLocal;
                //this.$store.commit('LOADER',false);
            }

        },

        getserie(){
            axios.get('/api/getSerieComprobante/'+this.generate_sales.header.id_tipo_comprobante)
            .then((response) => {
                this.serie = response.data;
                //this.generate_sales.header.id_medio_pago = this.metodospago[0].id_medio_pago;
                this.generate_sales.header.id_serie = this.serie[0].id_serie;
            })
            .catch( resonse => {
                console.log(resonse);
            }) 
        },

        getMesa(){

            axios.get('/api/table/'+this.id_mesa)
            .then((response) => {
                this.mesa = response.data;
            })
            .catch( resonse => {
                console.log(resonse);
            }) 
        },

        getOrdenesMesa(){
            this.$store.commit('LOADER',true);
            axios.get('/api/mesaorden/'+this.id_mesa)
            .then((response) => { 
                this.tabs_form = [];
                this.tabs_form = response.data;
                //
                if(this.mozos.length==1 && this.tabs_form.length==0){
                    axios.get('/api/user/')
                    .then((response) => {
                        //if(response.data.roles[0]=='mozo') { 
                            //this.crearOrdenMozo(response.data.id)
                            //this.nombre_mozo = response.data.nombre;//obj[0].name;
                            //this.$store.commit('LOADER',false);
                            /*this.add();*/
                        //};
                        this.id_mozo_sel = this.mozos[0].id;
                        this.crearOrdenMozo(this.mozos[0].id);
                    });
                }  
                if(this.tabs_form.length>0){
                    if((!this.validateAccess()) && (!this.validaccess)){
                        this.dialogAccess = true; 
                    }
                }
                for (let index = 0; index < this.tabs_form.length; index++) {
                    this.nombre_mozo = this.tabs_form[index].orden.usuario.name;

                    for (let j = 0; j < this.tabs_form[index].orden.detalle.length; j++) {
                        this.tabs_form[index].orden.detalle[j].total = Number(this.tabs_form[index].orden.detalle[j].precio * this.tabs_form[index].orden.detalle[j].cantidad).toFixed(2);
                    }
                    var sumaTotal;
                    sumaTotal = this.tabs_form[index].orden.detalle.reduce(function (sum, product) {
                        var total_fila = parseFloat(product.total);
                        if (!isNaN(total_fila)) {
                            return sum + total_fila;
                        }
                    }, 0);
                    
                    if(!isNaN(sumaTotal)){
                        this.tabs_form[index].orden.subtotal = parseFloat(sumaTotal/1.10).toFixed(2);
                        this.tabs_form[index].orden.igv = parseFloat(sumaTotal -  this.tabs_form[index].orden.subtotal).toFixed(2);
                        this.tabs_form[index].orden.total = parseFloat(sumaTotal).toFixed(2);
                    }else{
                        this.tabs_form[index].orden.igv = '';
                        this.tabs_form[index].orden.subtotal = '';
                        this.tabs_form[index].orden.totall = '';
                    }
                }

                this.$store.commit('LOADER',false);
            })
            .catch( (error) => {
                console.error(error);
                this.$store.commit('LOADER',false);
            }) 
        },

        getCustomers(){
            axios.get('/api/searchCustomer/')
            .then((response) => {
                this.customers=response.data.entries;
            })
            .catch( resonse => {
                console.log(resonse);
            }) 
        },
        
        //TABS ORDENES
        add() {
            this.$store.commit('LOADER',true);
            axios.post('/api/order',{id_mesa:this.id_mesa,numero_comensales:0, id_mozo: this.id_mozo_sel})
            .then((response) => {
                this.tabs_form.push(response.data);
                this.id_mozo_sel = null;
                this.tab = this.tabs_form.length-1;
                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                this.getOrdenesMesa();
                console.log(resonse);
                this.$store.commit('LOADER',false);
            });

        },

        getCategorias(){
                axios.get('/api/getCategoriasTipoSucursal/1')
                .then((response) => {
                    var array1 = response.data;
                    this.categorias = this.categorias.concat(array1);

                    //localStorage.setItem('categorias', JSON.stringify(this.categorias));
                })
                .catch( resonse => {
                    console.log(resonse);
                }) 
            
        },

        getProductos(id_categoria){
                axios.get('/api/getProductosSucursal/'+id_categoria)
                .then((response) => {
                    this.productos = response.data;
                    localStorage.setItem('productos', JSON.stringify(response.data));
                })
                .catch( resonse => {
                    console.log(resonse);
                }) 
        },

        addItemOrder(item){
            if(this.tabs_form[this.tab] == undefined){
                return;
            }
            
            this.$store.commit('LOADER',true);
            const ind = this.tabs_form[this.tab].orden.detalle.findIndex((element) => element.id_producto == item.id_producto);
            /*item.cantidad = 1;
            item.precio = Number(item.precio).toFixed(2);
            item.total = Number(item.precio).toFixed(2);*/
            if(ind==-1){
                let detalle={
                    id_producto: item.id_producto,
                    cantidad: 1,
                    precio: Number(item.precio).toFixed(2),
                    observaciones: "",
                    id_orden: this.tabs_form[this.tab].id_orden
                }
                axios.post('/api/orderdetail',detalle)
                .then((response) => {
                    this.tabs_form[this.tab].orden.detalle.push(JSON.parse(JSON.stringify(response.data)));
                    this.calcularTotalFila(this.tabs_form[this.tab].orden.detalle.length-1);
                    this.calcularTotal();
                    this.$store.commit('LOADER',false);
                })
                .catch( resonse => {
                    this.$store.commit('LOADER',false);
                    console.log(resonse);
                });
            }
            else{
                this.addItem(ind);
                this.$store.commit('LOADER',false);
            }
        },
        calcularTotalFila(indx){
            this.tabs_form[this.tab].orden.detalle[indx].total = Number(this.tabs_form[this.tab].orden.detalle[indx].precio * this.tabs_form[this.tab].orden.detalle[indx].cantidad).toFixed(2);
            let id_detalle=this.tabs_form[this.tab].orden.detalle[indx].id_detalle_orden;
            let val = this.tabs_form[this.tab].orden.detalle[indx].cantidad;
            axios.put('/api/updateFieldOrdenDetalle/'+id_detalle, { field:'cantidad', value:val })
            .then((response) => {
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            });
            this.calcularTotal();

        },
        calcularTotal() {
            var sumaTotal;
            //sumaTotal = this.form.detail.reduce(function (sum, product) {
            //axios.put('/api/updateFieldOrden/'+this.tabs_form[this.tab].orden.id_orden, { field:'status', value:0});
            sumaTotal = this.tabs_form[this.tab].orden.detalle.reduce(function (sum, product) {
                var total_fila = parseFloat(product.total);
                if (!isNaN(total_fila)) {
                    return sum + total_fila;
                }
            }, 0);
            
            if(!isNaN(sumaTotal)){
                if(this.generate_sales.header.total_dscto != null){
                    var total_dsct = parseFloat(this.generate_sales.header.total_dscto).toFixed(2);
                    sumaTotal = parseFloat(sumaTotal - total_dsct).toFixed(2);
                }

                this.tabs_form[this.tab].orden.subtotal = parseFloat(sumaTotal/1.10).toFixed(2);
                this.tabs_form[this.tab].orden.igv = parseFloat(sumaTotal -  this.tabs_form[this.tab].orden.subtotal).toFixed(2);
                this.tabs_form[this.tab].orden.total = parseFloat(sumaTotal).toFixed(2);
            }else{
                this.tabs_form[this.tab].orden.igv = '';
                this.tabs_form[this.tab].orden.subtotal = '';
                this.tabs_form[this.tab].orden.totall = '';
            }
        },
        //PUT ITEM
        addItem(indx){
            this.tabs_form[this.tab].orden.detalle[indx].cantidad++;
            this.calcularTotalFila(indx);
        },
        removeItem(indx){
            if(this.tabs_form[this.tab].orden.detalle[indx].cantidad==1){ return }
            this.tabs_form[this.tab].orden.detalle[indx].cantidad--;
            this.calcularTotalFila(indx);
        },
        deleteItem(indx){
            axios.delete('/api/orderdetail/'+this.tabs_form[this.tab].orden.detalle[indx].id_detalle_orden)
            .then((response) => {
                this.tabs_form[this.tab].orden.detalle.splice(indx,1);
                //DELETE ITEM
                this.calcularTotal();
            })
            .catch( resonse => {
                console.log(resonse);
            });
        },
        updateCustomer(id){
            axios.put('/api/updateFieldOrden/'+id, { field:'id_cliente', value:this.tabs_form[this.tab].orden.id_cliente })
            .then((response) => {
            this.observationDialog=false;
            })
            .catch( resonse => {
                console.log(resonse);
            });
        },
        editObservation(indx){
            this.observationDialog=true;
            this.indxobservation=indx;
        },

        updateObservation(){
            let id_detalle=this.tabs_form[this.tab].orden.detalle[this.indxobservation].id_detalle_orden;
            let val = this.tabs_form[this.tab].orden.detalle[this.indxobservation].observaciones;
            axios.put('/api/updateFieldOrdenDetalle/'+id_detalle, { field:'observaciones', value:val })
            .then((response) => {
            this.observationDialog=false;
            })
            .catch( resonse => {
                console.log(resonse);
            });
        },

        //GENERAR COMPROBANTE
        preLoadData(){
            this.$store.commit('LOADER',true);
            let id_orden=this.tabs_form[this.tab].id_orden;
            axios.get('/api/preLoadData/'+id_orden)
            .then((response) => {
                //var obj = Object.assign(o1, o2, o3);
                //this.generate_sales.header = response.data;
                this.generate_sales.header = Object.assign(this.tabs_form[this.tab].orden, response.data);
                this.generate_sales.header.id_tipo_comprobante = this.tipo_comprobante[2].id_tipo_comprobante;
                this.generate_sales.header.id_medio_pago = this.metodospago[0].id_medio_pago;
                this.generate_sales.header.id_moneda = this.monedas[0].id_moneda;
                this.generate_sales.header = JSON.parse(JSON.stringify(this.generate_sales.header));
                this.getserie();

                this.generate_sales.header.total_efectivo = this.tabs_form[this.tab].orden.total;
                this.generate_sales.header.total_vuelto = parseFloat(0).toFixed(2);
                this.generate_sales.header.total_dscto = parseFloat(0).toFixed(2);
                
                this.comprobanteDialog=true;
                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                console.log(resonse);
                this.$store.commit('LOADER',false);
            });
        },

        async saveComprobante(){
            if(this.$refs.form.validate()){
                this.generate_sales.detail = [];
                this.loading=true;
                this.$store.commit('LOADER',true);
                let id_orden=this.tabs_form[this.tab].id_orden;
                const element = this.tabs_form[this.tab].orden.detalle;
                if(this.row == 'radio1'){
                    for (let index = 0; index < element.length; index++) {
                        const orden_detalle = element[index];
                        this.generate_sales.detail.push({
                            id_producto: orden_detalle.id_producto,
                            id_unidad_medida: orden_detalle.producto.id_unidad_medida,
                            nombre_producto: orden_detalle.producto.nombre_producto,
                            nombre_producto: orden_detalle.producto.nombre_producto,
                            cantidad: orden_detalle.cantidad,
                            precio_unitario: orden_detalle.precio,
                            codigo_producto: orden_detalle.producto.codigo_producto,
                            precio_total: orden_detalle.total,
                            tipo_producto: orden_detalle.producto.tipo_producto_combo
                        });
                    }
                    axios.post('/api/storeByOrder/'+id_orden,  this.generate_sales)
                    .then((response) => {
                        if(response.data.success){
                            this.generate_sales.header = {};
                            this.generate_sales.detail = [];
                            this.comprobanteDialog = false;
                            if(response.data.print == 1){
                                printJS({printable:response.data.pdf, type:'pdf', maxWidth:80});
                                this.loading=false;
                                this.$store.commit('LOADER',false);
                            }
                            else{
                                window.open(response.data.pdf);
                                this.loading=false;
                                this.$store.commit('LOADER',false);
                            }
                            this.$router.push('/tablesSystem/'+this.mesa.id_sucursal);
                            // this.urlComprobante = response.data.pdf
                            // this.verComprobanteDialog = true;
                            // this.$store.commit('LOADER',false);
                            // this.getOrdenesMesa();
                            // this.loading=false;
                            // this.close();
                        }
                        else{
                            this.$store.commit('LOADER',false);
                            const error = JSON.parse(response.data.error);
                            this.$swal({
                                icon:'error',
                                title:'Opps!',
                                text:error.message,
                                timerProgressBar:true,
                                timer:2500
                            });
                        }
                    })
                    .catch( resonse => {
                        this.loading=false;
                        this.$store.commit('LOADER',false);
                        console.log(resonse);
                    });
                }
                if(this.row == 'radio2'){
                    let data = []
                    for (let index = 0; index < element.length; index++) {
                        const orden_detalle = element[index];
                        data.push({
                            id_producto_servicio: orden_detalle.id_producto,
                            nombre_producto_servicio: orden_detalle.producto.nombre_producto,
                            cantidad: orden_detalle.cantidad,
                            precio_unitario: orden_detalle.precio,
                            precio_total: orden_detalle.total,
                            observaciones: orden_detalle.observaciones,
                            pago: 0,
                            fecha_hora: new Date().toISOString().substr(0, 10),
                            id_reservas: this.habitacion.id_reservas,
                        });
                    }
                    try{
                        const response = await API.bookings.detail({detalle:data});
                        this.deleteOrder = this.tabs_form[this.tab].orden.id_orden;
                        this.deleteOrdenConfirm(0);
                        this.$swal({
                            toast: true,
                            position: 'top-end',
                            icon:'success',
                            title:'Consumo registrado correctamente',
                            showConfirmButton:false,
                            timerProgressBar:true,
                            timer:2500
                        });
                        this.$router.push('/tablesSystem/'+this.mesa.id_sucursal);
                        this.$store.commit('LOADER',false);
                    }catch(error){
                        this.$store.commit('LOADER',false);
                        console.error(error);
                    }
                }
            }
            else{
                return;
            }
        },
        close(){
            this.generate_sales.header = {};
            this.generate_sales.detail = [];
            this.comprobanteDialog = false; 
        },

        deleteMesaOrden(id){
            this.deleteOrder = id;
            this.dialogDelete = true;
        },
        async deleteOrdenConfirm(id){
            try{
                await API.table_order.remove(this.deleteOrder);
                this.dialogDelete = false;
                this.getOrdenesMesa();
            }catch(error){
                console.error(error.response.status);
                this.dialogDelete = false;
            }
        },

        printCocina(tipo=0){
            let id_orden=this.tabs_form[this.tab].id_orden;
            let id_sucursal_mesa = this.mesa.id_sucursal;
            let headers_send = {headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Access-Control-Allow-Origin': '*'
            }};

            this.$store.commit('LOADER',true);
            let urlPath = tipo==0 ? '/api/itemsPrintCocina/' : '/api/itemsPrintBarra/';

            axios.get( urlPath + id_orden)
            .then((response) => {

                let datos_print = response.data;
                console.log(response.data)
                //Imprimimos en cocina, apuntamos al WS instalado localmente
                axios.post(datos_print.url_print,  datos_print, headers_send)
                .then((response2) => {
                    this.$router.push('/tablesSystem/'+id_sucursal_mesa);
                })
                .catch( resonse2 => {
                    //console.log(this.mesa.id_sucursa);
                    //this.$router.push('/tablesSystem/'+id_sucursal_mesa);
                    console.log(resonse2);
                });

                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                this.$store.commit('LOADER',false);
                console.log(resonse);
            });

        },

        printCuenta(id_orden){
                        
            axios.get('/api/updateStatus/'+id_orden)
                .then((response) => {
                    
                })
                .catch( resonse => {
                    console.log(resonse);
                })
            this.urlComprobante = null;
            this.$store.commit('LOADER',true);
            this.urlComprobante = '/generarTicketCuentaPDF/'+id_orden
            this.verComprobanteDialog = true;
            this.$store.commit('LOADER',false);
        },

        getTiposDoc(){
            let tiposDocLocal = JSON.parse(localStorage.getItem('tiposdoc'));

            if(tiposDocLocal == null){
                axios.get('/api/getTiposDoc')
                .then((response) => {
                    this.tipos_doc = response.data;
                    localStorage.setItem('tiposdoc', JSON.stringify(this.tipos_doc));
                })
                .catch( resonse => {
                    console.log(resonse);
                })
            }else{

                this.tipos_doc = tiposDocLocal;
                
            }
            
        },

        async SearchApi(){
          
            // console.log(this.nuevoCliente.data); 
            this.$store.commit('LOADER',true);
            
            this.datosBus.tipoDoc = this.nuevoCliente.id_tipo_doc;
            this.datosBus.numDoc = this.nuevoCliente.nro_doc;
            console.log(this.datosBus.tipoDoc); 

          if (this.datosBus.tipoDoc === 1 && this.datosBus.numDoc.length !== 11) {
            this.$store.commit('LOADER', false);
            this.$swal({ 
              icon: 'error',
              title: 'Error',
              text: 'Verifique los datos ingresados son correctos',
              timerProgressBar: true,
              timer: 3000
            });
            return;
          }

          if (this.datosBus.tipoDoc === 2 && this.datosBus.numDoc.length !== 8) {
            this.$store.commit('LOADER', false);
            this.$swal({ 
              icon: 'error',
              title: 'Cliente no encontrado',
              text: 'Verifique los datos ingresados son correctos',
              timerProgressBar: true,
              timer: 3000
            });
            return;
          }
          try {
              const rpta = await axios.post("/api/buscarDniRuc", this.datosBus);
              
              if (rpta.data.success) {
                  let nombreCliente = "";
                  let direccionCliente = rpta.data.direccion_completa || "";

                  if (this.datosBus.tipoDoc === 1) {
                      nombreCliente = rpta.data.nombre_o_razon_social || "Nombre no disponible";
                  } else if (this.datosBus.tipoDoc === 2 && rpta.data.result) {
                      nombreCliente = `${rpta.data.result.Nombres || ""} ${rpta.data.result.Apellidos || ""}`.trim();
                  }

                  // Asigna los valores recuperados
                  this.nuevoCliente.nombre = nombreCliente;
                  this.nuevoCliente.direccion = direccionCliente;
                  
                  // Indica que la carga finalizó
                  this.$store.commit('LOADER', false);
                  
              } else {
                  throw new Error("Cliente no encontrado");
              }
          } catch (error) {
              console.error("Error en la búsqueda de DNI/RUC:", error);
              
              this.$swal({ 
                  icon: 'error',
                  title: 'Cliente no encontrado',
                  text: 'No se pudo recuperar la información de la persona. Por favor ingrésela manualmente.',
                  timerProgressBar: true,
                  timer: 4000
              });
          } finally {
              this.$store.commit('LOADER', false);
          }


        },

        addNuevoCliente(id_orden){
            this.selectedOrden = id_orden;
            this.nuevoClienteDialog = true;
        },
        async guardarCliente() {
            try {
                if (!this.$refs.formCliente.validate()) return;
                
                this.$store.commit('LOADER', true);
                console.log(this.nuevoCliente) // sale undefined
                const response = await axios.post("/api/customer", this.nuevoCliente);

                if (response.data.allowed.status) {
                    const id_cliente = response.data.cliente.id_cliente;

                    // Actualiza el cliente en la orden
                    await axios.put(`/api/updateFieldOrden/${this.selectedOrden}`, {
                        field: 'id_cliente',
                        value: id_cliente
                    });

                    // Refresca la lista de clientes
                    const response3 = await axios.get('/api/searchCustomer');
                    this.customers = response3.data.entries;

                    // Actualiza el estado de la orden en Vuex
                    this.$store.commit('UPDATE_ORDEN', {
                        id_orden: this.selectedOrden,
                        id_cliente: id_cliente,
                        cliente: response.data.cliente
                    });

                    console.log(JSON.stringify(response.data.cliente));
                } else {
                    this.$swal({
                        icon: 'info',
                        // title: 'Oops!',
                        text: response.data.allowed.message,
                        timerProgressBar: true,
                        timer: 2500
                    });
                }
                this.getCustomers();
            } catch (error) {
                console.error("Error al guardar cliente:", error);
                this.$swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al procesar la solicitud.',
                    timerProgressBar: true,
                    timer: 2500
                });
            } finally {
                this.nuevoClienteDialog = false;
                this.$store.commit('LOADER', false);
            }
        },


        closeCustomer(){
            this.nuevoClienteDialog = false;
            this.$refs.formCliente.reset();
        },
        async calcularVuelto(tab){
            let total = this.tabs_form[tab].orden.total;
            let efectivo = this.generate_sales.header.total_efectivo;

            let vuelto = parseFloat(efectivo - total).toFixed(2);
            
            this.generate_sales.header.total_vuelto = vuelto;

            this.$set( this.generate_sales, vuelto, vuelto); 

        },

        async calcularDscto(tab){
            let total = this.tabs_form[tab].orden.total;
            let dscto = this.generate_sales.header.total_dscto;

            let fin_total = parseFloat(total - dscto).toFixed(2);
            this.tabs_form[tab].orden.total = fin_total;

            this.calcularTotal();

            //this.generate_sales.header.total_efectivo = fin_total;

            this.$set( this.tabs_form[tab].orden, fin_total, fin_total); 

            this.generate_sales.header.subtotal = this.tabs_form[tab].orden.subtotal;
            this.generate_sales.header.igv = this.tabs_form[tab].orden.igv;
            this.generate_sales.header.total = this.tabs_form[tab].orden.total;

        }

  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
*{
    text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important;
}
.v-application .subtitle-2 {

    font-family:'Quicksand', sans-serif  !important;
}
  .theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr:hover:not(.v-data-table__expanded__content):not(.v-data-table__empty-wrapper) {
    background: #f8e2b6;
}
</style>

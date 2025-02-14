<template>
    <div class="pa-2">
        <v-autocomplete
            v-model="entry"
            :items="items"
            :loading="isLoading"
            :search-input.sync="search"
            hide-no-data
            hide-selected
            hide-details
            item-text="Description"
            item-value="API"
            label="Busqueda de productos"
            placeholder="Inicia a escribir para buscar"
            v-on:input="handleInput($event)"
            clearable
            filled
            return-object   
        ></v-autocomplete>
    </div> 
</template>

<script>
export default {
    data(){
        return {
            entry:null,
            entries: [],
            search:null,
            isLoading: false,
        }
    },
    computed:{
        items () {
            return this.entries.map((entry) => {
                const Description = entry.nombre_producto;
                return Object.assign({}, entry, { Description });
            });
        },
    },
    methods:{
        handleInput(event){
            if(event != null){
                this.$emit("changeInput", event);
                this.entry = null;
            }
        }
    },
    watch:{
        search (val) {
            //console.log(val);
            if (this.items.length > 0) return
            if (this.isLoading) return
            this.isLoading = true
            fetch('/api/getProductosSucursalFetch')
            .then(res => res.json())
            .then(res => {
                const { count, entries } = res
                this.count = count
                this.entries = entries
            })
            .catch(err => {
                console.log(err)
            })
            .finally(() => (this.isLoading = false))
        },
    }
}
</script>

<style>

</style>
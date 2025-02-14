<template>
    <div>
        <div class="text-center"><h2>{{year}}</h2></div>
        <v-row no-gutters>
            <v-col
                v-for="(month,i) in data"
                :key="i"
                cols="12"
                sm="4"
                 class="text-center"
            >
                <v-card
                class="pa-2"
                outlined
                tile
                >
                    <div>
                        {{capitalize(month.month_name)}}
                    </div>
                    <v-chip
                        class="ma-2"
                        color="cyan lighten-2 white--text"
                        label
                    >
                        S/.{{capitalize(month.incomes)}}
                    </v-chip>
                    <v-chip
                        class="ma-2"
                        color="red lighten-1 white--text"
                        label
                    >
                        S/.{{capitalize(month.expenses)}}
                    </v-chip>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
<script>
export default {
    data: () => ({ 
      drawer: null,
      cards: {},
      year: new Date().getFullYear(),
      data:[],
    }),
    methods:{
        async getData(){
            try{
                const data = await API.dashboard.year();
                //this.cards = data.data;
                console.log(data.data);
                this.data = data.data;
                this.$store.commit('LOADER',false);
            }catch(error){
                console.error(error);
                this.$store.commit('LOADER',false);
            }
        },
        capitalize(word) {
            return word[0].toUpperCase() + word.slice(1).toLowerCase();
        }
    },
    created(){
      this.getData();
    }
}
</script>
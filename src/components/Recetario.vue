<template>
  <div>
    <div class="columns">
      <div class="column">
        <b-field label="Buscar receta">
          <b-input
            @keyup.native="buscar()"
            v-model="busqueda"
            placeholder="Ingresa el término de búsqueda"
            icon-right="close-circle"
            icon-right-clickable
            @icon-right-click="cancelarBusqueda()"
            :loading="cargando"
          ></b-input>
        </b-field>
      </div>
    </div>
    <div class="columns is-multiline is-desktop">
      <div
        class="column is-one-third-desktop"
        v-for="(receta, i) in recetas"
        :key="i"
      >
        <tarjeta-receta :receta="receta"></tarjeta-receta>
      </div>
      <div class="column" v-if="recetas.length === 0 && !cargando">
        <p>No se encontraron recetas.</p>
      </div>
    </div>
  </div>
</template>

<script>
import RecetasService from "../services/RecetasService";
import TarjetaReceta from "./TarjetaReceta.vue";
import Utiles from "../services/Utiles";

export default {
  components: { TarjetaReceta },
  data() {
    return {
      recetas: [],
      cargando: false,
      busqueda: "",
    };
  },
  async mounted() {
    await this.obtenerRecetas();
    this.buscar = Utiles.debounce(this.buscarRecetas, 500);
  },
  methods: {
    async cancelarBusqueda() {
      if (!this.busqueda) return;
      this.busqueda = "";
      await this.obtenerRecetas();
    },
    async obtenerRecetas() {
      try {
        this.cargando = true;
        this.recetas = await RecetasService.obtenerRecetas();
      } catch (error) {
        this.$buefy.toast.open({
          message: "Error al obtener recetas",
          type: "is-danger",
        });
      } finally {
        this.cargando = false;
      }
    },
    async buscarRecetas() {
      if (!this.busqueda) {
        await this.cancelarBusqueda();
        return;
      }
      try {
        this.cargando = true;
        this.recetas = await RecetasService.buscarRecetas(this.busqueda);
      } catch (error) {
        this.$buefy.toast.open({
          message: "Error al buscar recetas",
          type: "is-danger",
        });
      } finally {
        this.cargando = false;
      }
    },
  },
};
</script>

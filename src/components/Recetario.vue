<template>
  <div>
    <div class="columns">
      <div class="column">
        <b-field label="Buscar receta">
          <b-input
            @input="buscarDebounce"
            v-model="busqueda"
            placeholder="Ingresa el término de búsqueda"
            icon-right="close-circle"
            icon-right-clickable
            @icon-right-click="cancelarBusqueda"
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
    </div>
    <div v-if="!recetas.length && !cargando" class="has-text-centered">
      No se encontraron recetas.
    </div>
    <div v-if="cargando" class="has-text-centered">
      Cargando...
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
      buscarDebounce: Utiles.debounce(this.buscar, 500),
    };
  },
  async mounted() {
    await this.obtenerRecetaFoto();
  },
  methods: {
    async obtenerRecetaFoto() {
      this.cargando = true;
      this.recetas = await RecetasService.obtenerRecetaFoto(); //
      this.cargando = false;
    },
    async buscar() {
      if (!this.busqueda) {
        await this.obtenerRecetaFoto();
        return;
      }
      
      try {
    this.cargando = true;
    this.recetas = await RecetasService.buscarRecetas(this.busqueda);
    this.cargando = false;

    // Verificar si no se encontraron resultados de búsqueda
    if (!this.recetas.length) {
      throw new Error("No se encontraron recetas.");
    }
  } catch (error) {
    // Manejar el error
    console.error(error);
    this.recetas = []; // Limpiar la lista de recetas
    this.cargando = false; // Dejar de mostrar el indicador de carga
  }
      
    },
    async cancelarBusqueda() {
      if (!this.busqueda) return;
      this.busqueda = "";
      await this.obtenerRecetaFoto();
    },
  },
};
</script>

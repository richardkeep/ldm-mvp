<template>
  <tr
    v-if="document"
    v-ripple
    class="range--row-item d-flex pa-0"
    @click="showLawsuitDocument"
  >
    <td
      scope="col"
      class="col"
      :class="{className, 'x-overlays': overlay}"
    >
      <div class="name">
        {{ documentName | truncate(20, '...') }}
      </div>
    </td>
    <td
      v-if="numberColumns === 3"
      scope="col"
      class="col col-4"
      :class="{'x-overlays': overlay}"
    >
      <div class="name">
        <span v-if="document.documentable">
          {{ document.submitter.name }}{{ documentIndex }}（{{ document.documentable.name | truncate(20, '...') }})
        </span>
        <span v-else>
          {{ document.submitter.name }}
        </span>
      </div>
    </td>
    <td
      scope="col"
      class="col d-flex last-column"
      :class="{'x-overlays': overlay}"
    >
      <div class="col-10 pa-0">
        <div class="name">
          {{ document.created_at_wareki }}
        </div>
      </div>
      <v-spacer />
      <sub-menu
        :key="'sub-menu-'+document.id"
        :sub-menu="subMenu"
        :sub-link="'/lawsuits/'+document.lawsuit_id+'/documents/'+document.id+'/edit'"
        :sub-id="document.id"
        :sub-type="'documents'"
        @update:overlay="overlay = $event"
      />
    </td>

    <v-overlay
      :z-index="zIndex"
      :value="overlay"
    />
  </tr>
</template>

<script>
  import subMenu from "../sub-menu"
  export default {
    name: "RangeRowItem",
    components:{
      subMenu
    },
    props: {
      document: {required: false, type: Object, default: () => {}},
      documentName: {required: false, type: String, default: () => ''},
      documentIndex: {required: false, type: Number, default: () => 0},
      subMenu: {required: false, type: Boolean, default: () => true},
      numberColumns: {required: false, type: Number, default: () => 2},
    },
    data() {
      return {
        className: 'col-6',
        overlay: false,
        zIndex: 8,
      }
    },
    watch: {
      overlay(val){
        if (val){
          for(let i = 0; i < rowItems.length; i++)
            rowItems[i].classList.add("cursor-unset");
        }else{
          for(let i = 0; i < rowItems.length; i++)
            rowItems[i].classList.remove("cursor-unset");
        }
      }
    },
    created() {
      this.className = 'col-' + (12/this.numberColumns);
    },
    mounted() {
      window.rowItems = document.getElementsByClassName('range--row-item');
    },
    methods: {
      /**
       * @function showLawsuitDocument
       * @description goto show documents of lawsuit
       */
      showLawsuitDocument() {
        const lawsuitId = this.document.lawsuit_id;
        let href = '/lawsuits/' + lawsuitId + '/documents?type='+this.document.type.id+'&name='+this.document.name;
        if(this.document.type.id === 2){
          const submitter = this.$route.params.submitter;
          const documentableId = this.document.documentable.id || 0;
          href = '/lawsuits/' + lawsuitId + '/documents?type='+this.document.type.id+'&submitter='+submitter+'&submitterId='+documentableId+
            '&documentId='+this.document.id+'&name='+this.document.name;
        }
        location.href = href;
      },
    }
  }
</script>

<style scoped>
  .x-overlays {
    z-index: 10;
    position: relative;
    background-color: #FFF;
  }
  .cursor-unset {
    cursor: unset !important;
  }
</style>


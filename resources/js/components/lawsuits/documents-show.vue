<template>
  <div class="container-fluid lawsuit-document-show">
    <lawsuit-header
      :lawsuit="lawsuit"
      :loading="loading"
    />
    <v-card class="lawsuit-document-card">
      <v-tabs
        v-model="typeTab"
        background-color="transparent"
        color="primary"
        grow
        centered
        hide-slider
        class="type-tabs"
        active-class="tab--activated"
        height="40"
      >
        <v-tab
          v-for="type in typeDocuments"
          :key="type.id"
        >
          {{ type.name }}
        </v-tab>
      </v-tabs>

      <v-tabs-items v-model="typeTab">
        <!-- ▽ 主張書面-->
        <claim-document-tab
          :documents="claimDocuments"
          :document-tab="claimDocumentTab"
        />
        <!-- △ 主張書面-->

        <!-- ▽ 証拠書面-->
        <evidence-document-tab
          :lawsuit="lawsuit"
          :documents="evidenceDocuments"
          :document-tab="evidenceDocumentsTab"
        />
        <!-- △ 証拠書面-->

        <!-- ▽ その他の書面-->
        <other-document-tab
          :documents="otherDocuments"
          :document-tab="otherDocumentTab"
        />
        <!-- △ その他の書面-->
      </v-tabs-items>
    </v-card>
  </div>
</template>
<script>
  import claimDocumentTab from './shared/claim-document-tab'
  import evidenceDocumentTab from './shared/evidence-document-tab'
  import otherDocumentTab from './shared/other-document-tab'
  import {mapState} from "vuex";

  export default {
    name: "LawsuitDocumentShow",
    components:{
      claimDocumentTab,
      evidenceDocumentTab,
      otherDocumentTab,
    },
    props: {
      typeDocuments: {required: true, type: Array, default: () => []},
    },
    data() {
      return {
        submitter: '',
        submitterKana: '',
        theadLabels: [
          { id: 1, label: '書面名', class: 'col-6'},
          { id: 2, label: '提出日', class: 'col-6'},
        ],
        lawsuit: {},
        typeTab: null,
        claimDocuments: [],
        claimDocumentTab: 0,
        evidenceDocuments: [],
        evidenceDocumentsTab: 0,
        otherDocuments: [],
        otherDocumentTab: 0,
        loading: true,
      }
    },
    computed: {
      ...mapState(['user']),
    },
    mounted() {
      // console.log(this.$options.name + ' mounted');
      // disable scroll-y
      document.body.style.overflowY = "hidden";
    },
    created() {
      /**
       * fetch lawsuit data from API
       */
      axios.get('users/'+this.user.id+'/lawsuits/'+this.$route.params.lawsuitId)
        .then(res => {
          this.lawsuit = res.data.data;
          const documents = this.lawsuit.documents;
          this.claimDocuments = documents.filter(d => d.type.description === 'claim');
          this.evidenceDocuments = documents.filter(d => d.type.description === 'evidence' );
          this.otherDocuments = documents.filter(d => d.type.description === 'other');

          // initial tab activated
          this.claimDocumentTab = this.initialActivatedTab(this.claimDocuments);
          this.initialEvidenceActivatedTab(this.lawsuit, this.evidenceDocuments);
          this.otherDocumentTab = this.initialActivatedTab(this.otherDocuments);
          this.loading = false;
          })
        .catch(err => {
          this.loading = true;
          console.log(err.response);
          alert('Not found data!');
        })
        .finally(() => {
        this.loading = false;
      });

      /**
       * initial typeTab
       */
      this.typeTab = this.$route.query.hasOwnProperty('type') ? parseInt(this.$route.query.type) - 1 : 0;
    },
    methods: {
      /**
       * @function initialActivatedTab
       * @description initial Activated Tab
       */
      initialActivatedTab(documents){
        if (this.$route.query.hasOwnProperty('type') && parseInt(this.$route.query.type) !== 2){
          const nameTab = this.$route.query.hasOwnProperty('name') ? this.$route.query.name : '';
          const tab = documents.findIndex(e => e.name === nameTab);
          return tab !== -1 ? tab : 0;
        }
        return 0;
      },

      /**
       * @function initialEvidenceActivatedTab
       * @description initial Activated Tab in evidence
       */
      initialEvidenceActivatedTab(lawsuit, documents){
        // evidenceDocumentsTab: 0,
        if (this.$route.query.hasOwnProperty('type') && parseInt(this.$route.query.type) === 2){
          const submitterId = this.$route.query.hasOwnProperty('submitterId') ? parseInt(this.$route.query.submitterId) : 0;
          const documentId = this.$route.query.hasOwnProperty('documentId') ? parseInt(this.$route.query.documentId) : 0;
          const documentName = this.$route.query.hasOwnProperty('name') ? this.$route.query.name : '';

          // document tab
          const _documents = this.parseEvidenceDocuments(documents.filter(d => d.documentable.id === submitterId));
          const documentsTab = _documents.findIndex(e => e.id === documentId && e.name === documentName);

          this.evidenceDocumentsTab = documentsTab !== -1 ? documentsTab : 0;
        }
      },

      /**
       * @function parseEvidenceDocuments
       * @description re order list documents
       */
      parseEvidenceDocuments(documents){
        // only "証拠説明書"
        let evidenceStatementDocuments = documents.filter(d => d.name === '証拠説明書').sort((a, b) => a.number > b.number ? 1 : -1);
        // except "証拠説明書"
        let evidenceDocuments = documents.filter(d => d.name !== '証拠説明書');
        evidenceDocuments.sort((a, b) => a.number >= b.number ? -1 : a.subnumber > b.subnumber ? 1 : -1).reverse();

        return evidenceStatementDocuments.concat(evidenceDocuments);
      }
    }
  }
</script>

<style scoped>
  .type-tabs {
    max-width: 60%;
    margin: 0 auto;
    border-bottom: 1.3px solid #dee2e6;
    padding-top: 1.28em;
  }
  .v-tabs-items{
    padding-top: 1.28em;
  }
  .tab--activated{
    font-weight: 600;
    border-bottom: 2px solid #1876D2;
  }
</style>

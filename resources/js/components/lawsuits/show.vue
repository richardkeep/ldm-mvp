<template>
  <div class="container-fluid lawsuit-show">
    <lawsuit-header
      :lawsuit="lawsuit"
      :loading="loading"
    />
    <v-row class="clearfix pr-5 pt-2">
      <v-col class="text-right">
        <v-btn
          v-ripple
          height="50"
          width="120"
          class="mr-0-auto btn-primary pa-3 height-auto font-size-16 font-weight-600"
          @click="showAllDocument(lawsuit.id)"
        >
          All View
        </v-btn>
      </v-col>
    </v-row>

    <div class="claim">
      <v-row>
        <v-col class="col-12 header-content">
          <h3 class="description font-size-18">
            主張書面
          </h3>
        </v-col>
      </v-row>
      <table class="table">
        <thead-columns :thead="tableHeadLabels" />
        <tbody>
          <template v-if="claimDocuments.length > 0">
            <range-row-item
              v-for="document in claimDocuments"
              :key="'claim-document-'+document.id"
              :document="document"
              :document-name="document.name"
              :document-index="submitterIndexFormatted(document)"
              :number-columns="3"
            />
          </template>
          <template v-else>
            <range-row-item />
          </template>
        </tbody>
      </table>
    </div>

    <div class="evidence-document">
      <v-row>
        <v-col class="col-12 header-content">
          <h3 class="description font-size-18">
            証拠書面
          </h3>
        </v-col>
      </v-row>
      <app-thead :thead="thead_evidence_document" />
      <app-type-lawsuit
        v-for="(plaintiff, index) in lawsuit.plaintiffs"
        :key="'document-plaintiff-'+plaintiff.id"
        :route-text="parseFolderName(plaintiff, ++index, 'plaintiff')"
        :route-link="parseRouteLink('plaintiff', plaintiff.id)"
      />
      <app-type-lawsuit
        v-for="(defendant, index) in lawsuit.defendants"
        :key="'document-defendant-'+defendant.id"
        :route-text="parseFolderName(defendant, ++index, 'defendant')"
        :route-link="parseRouteLink('defendant', defendant.id)"
      />
    </div>

    <div class="other-documents">
      <v-row>
        <v-col class="col-12 header-content">
          <h3 class="description font-size-18">
            その他の書面
          </h3>
        </v-col>
      </v-row>
      <table class="table">
        <thead-columns :thead="tableHeadLabels" />
        <tbody>
          <template v-if="otherDocuments.length > 0">
            <range-row-item
              v-for="(document, index) in otherDocuments"
              :key="'other-document-'+document.id"
              :document="document"
              :document-name="document.name"
              :document-index="++index"
              :number-columns="3"
            />
          </template>
          <template v-else>
            <range-row-item />
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
  import {mapState} from "vuex";

  export default {
    name: "LawsuitShow",
    props: {
      routeSubmitterDocumentsIndex: {
        required: true,
        type: String,
        default: () => ''
      },
      submitters: {required: true,  type: Array, default: () => []},
    },
    data() {
      return {
        tableHeadLabels: [
          { id: 1, label: '書面名', class: 'col-4'},
          { id: 2, label: '提出者', class: 'col-4'},
          { id: 3, label: '提出日', class: 'col-4'},
        ],
        thead_evidence_document: [{ id: 1, name: 'フォルダ名', class: 'col-12'}],
        thead_other_documents: [{ id: 1, name: '書面名', class: 'col-12'}],
        lawsuit: {},
        claimDocuments: [],
        evidenceDocuments: [],
        otherDocuments: [],
        loading: true,
			}
    },
    computed: {
      ...mapState(['user']),
    },
    created() {
      axios.get('users/'+this.user.id+'/lawsuits/'+this.$route.params.lawsuitId)
      .then(res => {
        this.lawsuit = res.data.data;
        this.claimDocuments = this.lawsuit.documents.filter(d => d.type.description === 'claim' );
        this.evidenceDocuments = this.lawsuit.documents.filter(d => d.type.description === 'evidence' );
        this.otherDocuments = this.lawsuit.documents.filter(d => d.type.description === 'other');
        this.loading = false;
      })
      .catch(err => {
        this.loading = true;
        console.log(err.response);
      }).finally(() => {
        this.loading = false;
      });
    },
    methods: {
      /**
       * @function submitterIndexFormatted
       */
      submitterIndexFormatted(document) {
        // console.log(document)
        if(document.hasOwnProperty('documentable') && document.documentable){
          let index = this.submitters.filter(s => s.submitter_id === document.submitter.id)
            .findIndex(s => s.id === document.documentable.id);
          return ++index;
        }
        return document.id;
      },
      /**
       * @function parseParties
       * @description get submitter of document
       *
       * @return string|null
       */
      parseParties(parties, condition){
        const hasDocument = this.evidenceDocuments.find(d => { return d.submitter.description === condition });
        if (hasDocument) return hasDocument.submitter.name + '書面';
        return null;
      },
      /**
       * @function parseFolderName
       * @description get parent of document
       *
       * @return string|null
       */
      parseFolderName(party, index, condition){
        const hasDocument = this.evidenceDocuments.find(d => {
          if (d.documentable)
            return d.submitter.description === condition && d.documentable.name === party.name
        });
        const partyType = condition === 'plaintiff' ? '原告' : '被告';
        if (hasDocument) return partyType + index + '書面';
        return null;
      },
      /**
       * @function parseRouteLink
       * @description generate route link to document
       *
       * @return string url
       */
      parseRouteLink(party, partyId){
        return this.routeSubmitterDocumentsIndex.replace('submitterId', partyId).replace('submitter', party);
      },
    }
  }
</script>

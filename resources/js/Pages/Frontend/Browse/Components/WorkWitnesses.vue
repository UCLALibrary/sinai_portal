<template>
  <template v-if="workWitnesses && workWitnesses.length > 0">
    <div v-for="workWit in workWitnesses" class="mb-8">
      <p>
        <strong>{{ workWit.work.title }}</strong><template v-if="workWit.locus && workWit.locus !== ''">; {{ workWit.locus }}</template>
      </p>
      <div class="indent">

        <p v-for="creator in workWit.work.creator">
          {{ creator.role.label }}: {{ creator.pref_name }}
        </p>

        <p v-if="workWit.work.alt_title && workWit.work.alt_title.length > 0">
          Alternate title: {{ workWit.work.alt_title.join(', ') }}
        </p>

        <p v-if="workWit.as_written && workWit.as_written !== ''">
          Transcribed Title: {{ workWit.as_written }}
        </p>

        <template v-if="workWit.excerpt && workWit.excerpt.length > 0">
          <p>
            Excerpts:
          </p>
          <div class="indent">
            <template v-for="excerpt in workWit.excerpt">
              <p>
                {{ excerpt.type.label }}, {{ excerpt.locus }}
              </p>
              <div class="indent">
                <p v-if="excerpt.as_written && excerpt.as_written !== ''">
                  Transcription: {{ excerpt.as_written }}
                </p>
                <template v-if="excerpt.translation && excerpt.translation.length > 0">
                  <p v-for="translation in excerpt.translation">
                    Translation: {{ translation }}
                  </p>
                </template>
                <p v-if="excerpt.note && excerpt.note.length > 0">
                  Note: {{ excerpt.note.join('; ') }}
                </p>
              </div>
            </template>
          </div>
        </template>

        <template v-if="workWit.note && workWit.note.length > 0">
          <p>
            Witness Notes:
          </p>
          <p v-for="note in workWit.note" class="indent">
            {{ note }}
          </p>
        </template>

        <template v-if="workWit.bib_editions && workWit.bib_editions.length > 0">
          <p>Editions</p>
          <div class="indent">
            <ResourcesEditions :editions="workWit.bib_editions" :display-label="false"/>
          </div>
        </template>

        <template v-if="workWit.bib_translations && workWit.bib_translations.length > 0">
          <p>Translations</p>
          <div class="indent">
            <ResourcesTranslations :translations="workWit.bib_translations" :display-label="false"/>
          </div>
        </template>

        <template v-if="workWit.bib_cites && workWit.bib_cites.length > 0">
          <p>Bibliography</p>
          <div class="indent">
            <ResourcesBibliographies :bibliographies="workWit.bib_cites" :display-label="false"/>
          </div>
        </template>

      </div>
    </div>
  </template>
</template>
<script>
import ResourcesEditions from "@/Pages/Frontend/Browse/Components/ResourcesEditions.vue";
import ResourcesBibliographies from "@/Pages/Frontend/Browse/Components/ResourcesBibliographies.vue";
import ResourcesTranslations from "@/Pages/Frontend/Browse/Components/ResourcesTranslations.vue";

export default {
  components: {ResourcesTranslations, ResourcesBibliographies, ResourcesEditions},
  props: {
    workWitnesses: {type: Array, required: true},
  },
};
</script>
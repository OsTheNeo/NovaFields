import IndexField from './components/belongs_to_many/IndexField'
import DetailField from './components/belongs_to_many/DetailField'
import FormField from './components/belongs_to_many/FormField'

import '../css/field.css'
Nova.booting((app) => {
  app.component('index-BelongsToManyField', IndexField)
  app.component('detail-BelongsToManyField', DetailField)
  app.component('form-BelongsToManyField', FormField)
})

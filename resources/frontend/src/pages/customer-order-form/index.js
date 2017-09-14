/*eslint no-unused-vars:0 */
import _base from 'src/pages/_base'
import components from './components'
import 'src/assets/customer-order-form.scss'

/*
 |------------------------------------------------
 | Components
 |------------------------------------------------
 | Attaching them to the root instance so they can
 | be used in all views without having to import
 */
_base.Vue.use(components)
_base.initialize()

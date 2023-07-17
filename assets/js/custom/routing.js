const routes = require('../../../public/resources/js/fos_js_routes.json');
const Routing = require('../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js');

Routing.setRoutingData(routes)

export default function path(route = '', params = {}) {
  const locale = document.documentElement.lang

  if (!Routing.routes_.hasOwnProperty(route) && Routing.routes_.hasOwnProperty(`${route}.${locale}`)) {
    route += `.${locale}`
  } else if (!Routing.routes_.hasOwnProperty(route)) {
    console.log(`ERROR ! Route ${route} not found.`)
  }
  return Routing.generate(route, params)
}

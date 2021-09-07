export default {
    directives: {
        'click-outside': {
            bind: function(el, binding) {
                const clickHandler = event => {
                    if (!el.contains(event.target) && el !== event.target) {
                        binding.value(event)
                    }
                }

                el.__vueClickEventHandler__ = clickHandler

                document.addEventListener('click', clickHandler)
            },
            unbind: function(el) {
                document.removeEventListener(
                    'click',
                    el.__vueClickEventHandler__
                )
            }
        }
    }
}

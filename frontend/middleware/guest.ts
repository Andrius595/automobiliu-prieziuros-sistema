import {useJWT} from "~/composables/useJWT";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const user = await useAuth().getUser()

    if (user) {
        return navigateTo('/');
    }
})
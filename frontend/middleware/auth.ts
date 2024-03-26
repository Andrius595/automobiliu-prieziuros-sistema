import {useJWT} from "~/composables/useJWT";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const token = await useJWT().getToken()

    if (!token) {
        return navigateTo('/login');
    }
})
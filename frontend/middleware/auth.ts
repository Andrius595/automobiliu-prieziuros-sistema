import {useJWT} from "~/composables/useJWT";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const user = await useAuth().getUser()

    console.log('user', user)

    if (!user) {
        return navigateTo('/login');
    }
})
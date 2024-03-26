import {useJWT} from "~/composables/useJWT";
import {useRoles} from "~/composables/useRoles";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { isServiceEmployee } = useRoles()

    if (!isServiceEmployee.value) {
        await navigateTo('/dashboard');
    }
})
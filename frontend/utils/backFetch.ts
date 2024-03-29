import type { UseFetchOptions} from "nuxt/app";
import {serialize} from "object-to-formdata";

export default async <DataT, ErrorT = any>(
    route: string,
    options: UseFetchOptions<DataT> & {sendsFiles?: boolean}
) =>
{
    const jwt = useJWT()

    let token = await jwt.getToken()

    if (!token) {
        await navigateTo('/login');
    }

    options.headers = {
        ...(options.headers || {}),
        Authorization: 'Bearer ' + token
    }
    if (options.sendsFiles) {
        (options.body as any)._method = options.method
        options.method = 'post'
        options.body = serialize(options.body)
    }
    if (!Object.hasOwn(options.headers as Object, 'Accept')) {
        options.headers = {
            ...options.headers,
            Accept: 'application/json'
        }
    }
    options.watch = false;

    return useFetch(useRuntimeConfig().public.apiURL + route, options) as ReturnType<(typeof useFetch<DataT, ErrorT>)>;
}
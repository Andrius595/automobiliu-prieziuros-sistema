import type { UseFetchOptions} from "nuxt/app";
import {SymbolKind} from "vscode-languageserver-types";

export default async <DataT, ErrorT = any>(
    route: string,
    options: UseFetchOptions<DataT>
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
    if (!Object.hasOwn(options.headers as Object, 'Accept')) {
        options.headers = {
            ...options.headers,
            Accept: 'application/json'
        }
    }
    options.watch = false;

    return useFetch(useRuntimeConfig().public.apiURL + route, options) as ReturnType<(typeof useFetch<DataT, ErrorT>)>;
}
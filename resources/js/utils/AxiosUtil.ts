import { type RequestInterface } from "@/interfaces/RequestInterface";
import { type ResponseInterface } from "@/interfaces/ResponseInterface";
import axios, { AxiosError, type AxiosResponse } from "axios";
import { reactive } from "vue";
import Cookies from "js-cookie";
import Page from "@/stores/Page";
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.baseURL = import.meta.env.APP_URL;

export default function useAxiosUtil<Input, Output>() {
    const request: RequestInterface<Output> = reactive({
        loading: false,
    });

    const getCsrfCookie = async () => {
        if (!Cookies.get("token_set")) {
            const expirationDate = new Date();
            expirationDate.setMinutes(expirationDate.getMinutes() + 120);
            await axios({
                url: "/sanctum/csrf-cookie",
                method: "GET",
            }).then(() => {
                Cookies.set("token_set", "1", {
                    expires: expirationDate,
                });
            });
        }
    };

    const setResult = (response: AxiosResponse | AxiosError) => {
        if (response instanceof AxiosError) {
            if (response.code === "ECONNABORTED") {
                request.message = response.message;
                request.status = 503;
            } else if (response.code === "ERR_NETWORK") {
                request.message = response.message;
                request.status = 503;
            }
            if (response.response) {
                request.status = response.response?.status;
                if (request.status === 419) {
                    Cookies.remove("token_set");
                }
                if (response.response.data) {
                    const res: ResponseInterface<Output> =
                        response.response.data;
                    request.message = res.message;
                    request.data = res.data;
                    request.errors = res.errors as Record<string, string[]>;
                    if (
                        request.status === 401 &&
                        res.message === "Unauthenticated."
                    ) {
                        Cookies.remove("token_set");
                        Page.user = null;
                    }
                }
            } else {
                request.message = response.message;
            }
        } else {
            request.status = response.status;
            request.message = response.data.message;
            request.data = response.data.data;
            request.errors = response.data.errors;
        }
    };

    const post = async (url: string, data: Input, useApi: boolean = true) => {
        request.loading = true;
        const settings = {
            url: useApi ? "/api/" + url : url,
            method: "POST",
            data: data,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        };
        await getCsrfCookie();
        await axios(settings)
            .then((response) => {
                setResult(response);
            })
            .catch((error) => {
                setResult(error);
            })
            .finally(() => {
                request.loading = false;
                axios.defaults.baseURL = import.meta.env.APP_URL;
            });
    };

    const get = async (url: string, query?: Input) => {
        request.loading = true;
        await getCsrfCookie();
        await axios({
            url: "/api/" + url,
            method: "GET",
            params: query,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            signal: AbortSignal.timeout(10000),
        })
            .then((response) => {
                setResult(response);
            })
            .catch((error) => {
                setResult(error);
            })
            .finally(() => {
                request.loading = false;
            });
    };

    const patch = async (url: string, data: Input) => {
        request.loading = true;
        await getCsrfCookie();
        await axios({
            url: "/api/" + url,
            method: "PATCH",
            data: data,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        })
            .then((response) => {
                setResult(response);
            })
            .catch((error) => {
                setResult(error);
            })
            .finally(() => {
                request.loading = false;
            });
    };

    const deleteRequest = async (url: string, data: Input) => {
        request.loading = true;
        await getCsrfCookie();
        await axios({
            url: "/api/" + url,
            method: "DELETE",
            data: data,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        })
            .then((response) => {
                setResult(response);
            })
            .catch((error) => {
                setResult(error);
            })
            .finally(() => {
                request.loading = false;
            });
    };

    const postFormData = async (url: string, data: Input) => {
        request.loading = true;
        await getCsrfCookie();
        await axios({
            url: "/api/" + url,
            method: "POST",
            data: data,
            headers: {
                Accept: "application/json",
                "Content-Type": "multipart/form-data",
            },
        })
            .then((response) => {
                setResult(response);
            })
            .catch((error) => {
                setResult(error);
            })
            .finally(() => {
                request.loading = false;
            });
    };

    const patchFormData = async (url: string, data: Input) => {
        request.loading = true;
        const form = data as FormData;
        form.append("_method", "PATCH");
        await getCsrfCookie();
        await axios({
            url: "/api/" + url,
            method: "POST",
            data: form,
            headers: {
                Accept: "application/json",
            },
        })
            .then((response) => {
                setResult(response);
            })
            .catch((error) => {
                setResult(error);
            })
            .finally(() => {
                request.loading = false;
            });
    };

    const getFile = async (url: string) => {
        request.loading = true;
        await getCsrfCookie();
        await axios({
            url: "/api/" + url,
            method: "GET",
            responseType: "blob",
        })
            .then((response) => {
                setResult(response);
                request.data = response.data;
            })
            .catch((error) => {
                setResult(error);
            })
            .finally(() => {
                request.loading = false;
            });
    };

    return {
        request,
        post,
        get,
        patch,
        postFormData,
        deleteRequest,
        patchFormData,
        getFile,
        axios,
    };
}

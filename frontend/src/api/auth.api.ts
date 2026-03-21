import { authApi } from "./client";

type LoginPayload = {
  email: string;
  password: string;
};

type LoginResponse = {
  access_token: string;
  token_type: string;
  expires_in: number;
};

type RegisterPayload = {
  name: string;
  email: string;
  password: string;
};

type Response = {
  message: string;
};

export const login = async (payload: LoginPayload): Promise<LoginResponse> => {
  const { data } = await authApi.post("/auth/login", payload);

  return data;
};

export const register = async (payload: RegisterPayload): Promise<Response> => {
  const { data } = await authApi.post("/auth/register", payload);

  return data;
};

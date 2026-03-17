import { createContext } from "react";

type Locale = "cs" | "en";

export type LocaleContextType = {
  locale: Locale;
  setLocale: (locale: Locale) => void;
};

export const LocaleContext = createContext<LocaleContextType>({
  locale: "cs",
  setLocale: () => {},
});

import { useContext } from "react";
import { LocaleContext } from "../../providers/LocaleContext.ts";

export const useLocale = () => useContext(LocaleContext);

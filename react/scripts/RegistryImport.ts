import { addRegistryItems, getRegistry } from "shadcn/registry";

const config = {
    "registries": {
        "@RSPDS": "http://localhost:5173/registry/{name}.json"
    }
};

const registry = await getRegistry("@RSPDS", {
    config,
    useCache: true,
});

const itemsToSkip = ["npmrc-config", "index", "date-range-picker-select", "date-range-picker", "primitive-calendar", "primitive-popover", "wp-to-date-fns-locale-map", "toggle", "primitive-toggle", "country-translations", "phone-number-input", "primitive-combobox"];

const registryItems = registry.items
    .filter((item) => (!(itemsToSkip.includes(item.name))))
    .map((item) => (`@RSPDS/${item.name}`));

await addRegistryItems(registryItems, {
    config,
    overwrite: true,
    silent: false,
});
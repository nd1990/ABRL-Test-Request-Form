# Location data

Static JSON used by the public quotation form for the dependent address dropdowns.

| File | Used for | Source |
| --- | --- | --- |
| `countries-states.json` | Country → State/Province options for every country in the country dropdown | [dr5hn/countries-states-cities-database](https://github.com/dr5hn/countries-states-cities-database) (`json/countries+states.json`) |
| `india-locations.json` | India → State → District → Taluka/Sub-district and State → City options | [planemad/india-local-government-directory](https://github.com/planemad/india-local-government-directory) (`administrative/1-state.csv`, `2-district.csv`, `3-subdistrict.csv`, `municipal/urban-local-body.csv`) |

The India file is based on the Local Government Directory (LGD) data published by the Government of India.

Notes:

- India uses the official LGD hierarchy. Districts/talukas are only available for India; for other countries the District and Taluka fields stay as free-text inputs.
- The form loads these files lazily with `fetch` and keeps working (falling back to text inputs) if either request fails.
- When refreshing the data, keep the same shape: `countries-states.json` is `{ "Country": ["State", ...] }` and `india-locations.json` is `{ "states": { "State": { "districts": { "District": ["Sub-district", ...] }, "cities": ["City", ...] } } }`.

export const DROPOFF_PLACE_ID = {
  ENTRANCE: 1,
  GARAGE: 2,
  BICYCLE: 3,
  OTHER: 4,
}

export const DROPOFF_HISTORY_SEARCH_TYPE = {
  ALL: 'all',
  MYSELF: 'myself',
}

export function scrollToTop(element = window) {
  element.scrollTo(0, 0)
}

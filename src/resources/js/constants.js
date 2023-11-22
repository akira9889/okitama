export const DROPOFF_PLACE_ID = {
  ENTRANCE: 1,
  ENTRANCE_WHEN_ABSENT: 2,
  GARAGE: 3,
  BICYCLE: 4,
  BOX: 5,
  OTHER: 6,
}

export const DROPOFF_HISTORY_SEARCH_TYPE = {
  ALL: 'all',
  MYSELF: 'myself',
}

export function scrollToTop(element = window) {
  element.scrollTo(0, 0)
}

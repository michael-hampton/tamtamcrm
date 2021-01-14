export default class BaseRepository {
    constructor () {
        this.errors = []
        this.error_message = ''

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        this.user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = this.user_account[0].account.settings
    }

    buildQueryParams (params) {
        var esc = encodeURIComponent
        return Object.keys(params)
            .map(k => esc(k) + '=' + esc(params[k]))
            .join('&')
    }

    handleError (error) {
        if (error.response && error.response.data.message) {
            this.error_message = error.response.data.message
        }

        if (error.response.data.errors) {
            this.errors = error.response.data.errors
        }
    }
}

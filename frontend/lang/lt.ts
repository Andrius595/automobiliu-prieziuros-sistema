const lt = {
    user: {
        first_name: 'Vardas',
        last_name: 'Pavardė',
        email: 'El. paštas',
        password: 'Slaptažodis',
        password_confirmation: 'Pakartokite slaptažodį',
        role: 'Rolė',
        create_user: 'Sukurti naudotoją',
        created_successfully: 'Naudotojas sėkmingai sukurtas',
        delete_user: 'Ištrinti naudotoją',
        confirm_delete_message: 'Ar tikrai norite ištrinti šį naudotoją?',
        deleted_successfully: 'Naudotojas sėkmingai ištrintas',
    },
    car: {
        make: 'Markė',
        model: 'Modelis',
        vin: 'VIN',
        year_of_manufacture: 'Pagaminimo metai',
        current_mileage: 'Dabartinis kilometražas',
        mileage_type: 'Kilometražo tipas',
        plate_no: 'Valstybinis numeris',
        color: 'Spalva',
        created_at: 'Sukurta',
        updated_at: 'Atnaujinta',
        owner: 'Savininkas',
        car: 'Automobilis',
        register_car: 'Registruoti automobilį',
        remove_car: 'Pašalinti automobilį',
        confirm_car_removal: 'Ar tikrai norite pašalinti šį automobilį?',
        general_information: 'Bendra informacija',
        history: 'Istorija',
        share_car: 'Dalintis automobiliu',
        transfer_car: 'Perleisti automobilį',
        share_history: 'Dalintis istorija',
    },
    service: {
        registrations: {
            confirm_registration: 'Patvirtinti registraciją',
            confirm_registration_message: 'Ar tikrai norite patvirtinti šią registraciją?',
        },
        employees: {
            employee: 'Darbuotojas',
            employees: 'Darbuotojai',
            create_employee: 'Sukurti darbuotoją',
            edit_employee: 'Redaguoti darbuotoją',
            delete_employee: 'Ištrinti darbuotoją',
            confirm_delete_message: 'Ar tikrai norite ištrinti šį darbuotoją?',
        },
        title: 'Pavadinimas',
        description: 'Aprašymas',
        service_title: 'Serviso pavadinimas',
        register_for_appointment: 'Registruotis į vizitą',
        edit_information: 'Redaguoti informaciją',
    },
    record: {
        short_description: 'Trumpas aprašymas',
        description: 'Išsamus aprašymas',
        delete_record: 'Ištrinti įrašą',
        confirm_delete_message: 'Ar tikrai norite ištrinti šį įrašą?',
        create_record: 'Sukurti įrašą',
    },
    appointment: {
        appointment: 'Vizitas',
        records: 'Aptarnavimo įrašai',
        complete_appointment: 'Užbaigti vizitą',
        check_blockchain_transaction: 'Patikrinti blokų grandinės transakciją',
        current_mileage: 'Dabartinis kilometražas',
        records_full_description: 'Aptarnavimo įrašų išsamus aprašymas',
        blockchain_transaction: 'Blokų grandinės transakcija',
        completed_at: 'Užbaigta',
        transaction_check_string: 'Transakcijos tikrinimo eilutė',
    },
    reminder: {
        reminders: 'Priminimai',
        title: 'Pavadinimas',
        description: 'Aprašymas',
        interval: 'Intervalas',
        last_reminded_at: 'Paskutinis primintas išiųstas',
        interval_type: 'Intervalo tipas',
        interval_types: {
            days: 'Dienos',
            weeks: 'Savaitės',
            months: 'Mėnesiai',
            years: 'Metai',
            mileage: 'Kilometražas'
        },
        new_reminder: 'Naujas priminimas',
        delete_reminder: 'Ištrinti priminimą',
        confirm_delete_message: 'Ar tikrai norite ištrinti šį priminimą?',
        edit_reminder: 'Redaguoti priminimą',
    },
    navigation: {
        cars_list: 'Automobilių sąrašas',
        services_list: 'Servisų sąrašas',
        users_list: 'Naudotojų sąrašas',
        services: {
            new_appointment: 'Naujas vizitas',
            registrations_list: 'Registracijų sąrašas',
            active_appointments: 'Aktyvūs vizitai',
            completed_appointments: 'Užbaigti vizitai',
            employees_list: 'Darbuotojų sąrašas',
            edit_service_information: 'Redaguoti serviso informaciją',
        },
    },
    tables: {
        actions: 'Veiksmai',
    },
    common: {
        save: 'Išsaugoti',
        cancel: 'Atšaukti',
        delete: 'Ištrinti',
        edit: 'Redaguoti',
        add: 'Pridėti',
        close: 'Uždaryti',
        confirm: 'Patvirtinti',
        aps: 'APS',
        aps_full: 'Automobilių priežiūros sistema',
        remove: 'Pašalinti',
        transfer: 'Perleisti',
        share: 'Dalintis',
        create: 'Sukurti',
    },
    auth: {
        login: 'Prisijungti',
        register: 'Registruotis',
        logout: 'Atsijungti',
        already_have_an_account: 'Jau turite paskyrą?',
        dont_have_an_account: 'Neturite paskyros?',
        forgot_password: 'Pamiršote slaptažodį?',
        reset_password: 'Atstatyti slaptažodį',
        remind_password: 'Priminti slaptažodį',
        send_reset_link: 'Siųsti atstatymo nuorodą',
    },
    roles: {
        client: 'Klientas',
        service_employee: 'Serviso darbuotojas',
        service_admin: 'Serviso administratorius',
        system_admin: 'Sistemos administratorius',
    }
}

export default lt
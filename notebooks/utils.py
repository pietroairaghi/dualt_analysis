import pandas as pd
import logging

import numpy as np

from fuzzywuzzy import fuzz
from fuzzywuzzy import process

import requests, json

import gender_guesser.detector as gender

import pickle


class Users:
    df = None
    df_raw = None
    
    def __init__(self):
        df = None

    def load_df(self, path = None, df = None):
        if path:
            self.df = pd.read_csv(path)
        elif df:
            self.df = df

    def preprocess_user_type(self):
        # One hot for user_type
        self.df = pd.concat(
            [self.df.loc[:, :'user_type'],
             (self.df['user_type'].str.split('\s*,\s*', expand=True)
              .stack()
              .str.get_dummies()
              .sum(level=0)),
             self.df.loc[:, 'classes':]],
            axis=1)

        vals_to_replace = {'formatore': 'supervisor', 'docente': 'teacher', 'studente': 'student'}
        self.df.rename(columns=vals_to_replace, inplace=True)

    def get_by_id(self,ids):
        return self.df[self.df['us_user'].isin(ids)]
        
    def get_only_latest_years(self,df):
        filter_grades = (((students['grade_2nd'].notnull()) & (students['grade_1st'].isnull())) | (students['grade_3rd'].notnull()) & ((students['grade_1st'].isnull()) | (students['grade_2nd'].isnull())))
        return df[filter_grades]

    def save_raw(self):
        self.df_raw = self.df.copy()

    def fillna(self,exclude=None):
        if exclude is None:
            exclude = ['user_name','start_year',
                       'start_semester', 'classes', 'companies',
                       'avg_activity_evaluations', 'avg_reflection_length',
                       'avg_specific_evaluations', 'avg_supervisor_evaluation'
                       ]

        cols = self.df.columns.difference(exclude,sort=False)
        self.df.loc[:, cols]= self.df.loc[:,cols].fillna(0)

        self.df.dropna(subset=['start_year','user_email','user_type'],inplace=True)

    def solve_duplicates(self):
        first = self.df[self.df.duplicated(['user_email'], keep='first')].sort_values(by="user_email")['us_user']
        last = self.df[self.df.duplicated(['user_email'], keep='last')].sort_values(by="user_email")['us_user']
        correct_map = dict(zip(first, last))

        to_sum = [col for col in self.df.columns if col.startswith("n_")]
        to_avg = [col for col in self.df.columns if col.startswith("avg_")]

        for new_id, old_id in correct_map.items():
            m = self.df['us_user'] == old_id
            n = self.df['us_user'] == new_id
            self.df.loc[m, to_sum] = self.df.loc[m, to_sum].values + self.df.loc[n, to_sum].values
            self.df.loc[m, to_avg] = (self.df.loc[m, to_avg].values + self.df.loc[n, to_avg].values) / 2

        to_drop = correct_map.keys()
        self.df = self.df.drop(self.df[self.df['us_user'].isin(to_drop)].index)

        self.correct_map = correct_map

    def load_correct_map(self,map_file_pickle):
        # Getting back the objects:
        with open(map_file_pickle,'rb') as f:
            self.correct_map = pickle.load(f)

    def correct_users(self,users):
        return users.replace(self.correct_map)

    def id_correction(self,df,id_column = 'us_user'):
        return df.replace({id_column:self.correct_map})

    def solve_gender(self,cache_file = "cache_gender.csv"):
        detector = gender.Detector(case_sensitive=False)
        try:
            cache_df = pd.read_csv(cache_file)
        except:
            cache_df = None
        self.df['gender'] = self.df['user_name'].apply(recognize_gender, detector=detector, cache_df = cache_df)

class Activities:
    df = None
    df_raw = None
    month_order = None
    month_map   = None

    def __init__(self):
        self.df = None

    def load_df(self,path):
        self.df = pd.read_csv(path)

        self.df['creation_date'] = pd.to_datetime(self.df['creation_date'])
        self.df['last_edit_date'] = pd.to_datetime(self.df['last_edit_date'])

    def save_raw(self):
        self.df_raw = self.df.copy()

    def load_map_month(self,month_map,month_order):
        self.month_order = month_order
        self.month_map = month_map

    def map_month(self,month_map,month_order):
        self.month_order = month_order
        self.month_map   = month_map
        self.df.replace({'creation_month': month_map}, inplace=True)
        self.df.replace({'last_edit_month': month_map}, inplace=True)
        self.df['month_order'] = self.df['creation_month'].map(dict(zip(month_order, range(1, 13))))
        self.df.sort_values(by=['month_order', 'activity_school_year'], inplace=True)

    def fillna(self,exclude=None):
        if exclude is None:
            exclude = ['activity_school_year','start_year',
                       'creation_date', 'creation_month', 'creation_year',
                       'last_edit_date', 'last_edit_month',
                       'last_edit_year', 'avg_specific_evaluations'
                       ]

        cols = self.df.columns.difference(exclude,sort=False)
        self.df.loc[:, cols]= self.df.loc[:,cols].fillna(0)

        self.df.dropna(subset=['start_year','creation_year'],inplace=True)

    def year_to_cat(self):
        cols = ['start_year', 'creation_year','activity_school_year']
        self.df[cols] = self.df[cols].astype('int64') #.astype('category')

    def drop_over_year(self):
        self.df.drop(self.df[self.df['activity_school_year']>3].index, axis=0, inplace=True)

    def get_over_year(self):
        return self.df[self.df['activity_school_year']>3]

    def sort_month(self,df,col,secondary_order=None):
        if secondary_order is None:
            secondary_order = []
        df['___month_order'] = df[col].map(dict(zip(self.month_order, range(1, 13))))
        df = df.sort_values(by=['___month_order']+secondary_order)
        return df.drop('___month_order',axis=1)

    def gby_date(self, by=None, event="creation"):
        if by is None:
            by = ['year', 'month']

        col = 'last_edit_date'
        if event == 'creation':
            col = 'creation_date'

        self.df['year'] = self.df[col]
        self.df['month'] = self.df[col]

        groupby = []
        if 'year' in by:
            groupby.append(pd.DatetimeIndex(self.df['year']).year)
        if 'month' in by:
            groupby.append(pd.DatetimeIndex(self.df['month']).month)

        return self.df.groupby(groupby)



def fuzzy_checker(wrong_options,correct_options):
    names_array=[]
    ratio_array=[]
    for wrong_option in wrong_options:
        if wrong_option in correct_options:
            names_array.append(wrong_option)
            ratio_array.append(100)
        else:
            x=process.extractOne(wrong_option,correct_options,scorer=fuzz.token_set_ratio)
            names_array.append(x[0])
            ratio_array.append(x[1])
    return names_array,ratio_array


def join_by_fuzzy(df, df_to_match,
                  original_column, to_match_column,
                  to_join_column, joined_column,
                  no_match_value=np.nan, limit=90):
    tmp_joined_name = '__fuzzy_result'

    str2Match = df[original_column].tolist()
    strOptions = df_to_match[to_match_column].tolist()
    name_match, ratio_match = fuzzy_checker(str2Match, strOptions)

    df1 = pd.DataFrame()
    df1['old_names'] = pd.Series(str2Match)
    df1['correct_names'] = pd.Series(name_match)
    df1['correct_ratio'] = pd.Series(ratio_match)
    equiv = df_to_match.set_index(to_match_column).to_dict()[to_join_column]
    df1.loc[:, tmp_joined_name] = df1["correct_names"].map(equiv)
    df1.loc[df1['correct_ratio'] < limit, tmp_joined_name] = no_match_value

    return df.assign(__fuzzy_result=df1[tmp_joined_name].values).rename({'__fuzzy_result': joined_column}, axis=1)



class Genderize:
    country_id = None

    def __init__(self, country_id=None):
        self.country_id = country_id

    def get_genders(self, names):
        url = ""
        cnt = 0
        if not isinstance(names, list):
            names = [names, ]

        for name in names:
            if url == "":
                url = "name[0]=" + name
            else:
                cnt += 1
                url = url + "&name[" + str(cnt) + "]=" + name

        if self.country_id:
            url += "&country_id=" + self.country_id

        req = requests.get("https://api.genderize.io?" + url)
        results = json.loads(req.text)

        retrn = []
        for result in results:
            if result["gender"] is not None:
                retrn.append((result["gender"], result["probability"], result["count"]))
            else:
                retrn.append((u'None', u'0.0', 0.0))
        return retrn

    def get_gender(self, name):
        res = self.get_genders([name])
        return res[0][0]


# names that are different wrt English
male_diff_names = ["Andrea", "Daniele"]

def recognize_gender(full_name, detector, weight=None, cache_df = None):
    logging.info(f"Recognizing gender for {full_name}: ")

    if cache_df is not None:
        try:
            cache_gender = cache_df.query(f'user_name == "{full_name}"')['gender']
            if len(cache_gender):
                cache_result = cache_gender.values[0]
                logging.info(f"\tfound in cache: {cache_result}")
                return cache_result
        except:
            logging.debug("Name not found in cache.")

    # because we are not sure about the name & last name
    male_pts = 0
    female_pts = 0
    names = full_name.split()

    st_fr = 1 if (weight == 'last') else -1

    for i, name in enumerate(names[::st_fr]):
        name = ''.join(e for e in name if e.isalnum())
        detection = detector.get_gender(name)
        if (detection == 'male' or detection == 'mostly_male') or name in male_diff_names:
            male_pts += (1 * (1 if weight is None else i))
        elif detection == 'female' or detection == "mostly_female":
            female_pts += (1 * (1 if weight is None else i))

    if male_pts > female_pts:
        logging.info("\tM")
        return 1
    if female_pts > male_pts:
        logging.info("\tF")
        return 0

    if weight is None:
        logging.info("\tRetrying with different weights..")
        res = recognize_gender(full_name, detector, weight='first')  # "first" because most of the
        # full names starts with the first name
        if res != np.nan:
            return res

    if not isinstance(detector, Genderize):
        logging.info("\tChange detector type..")
        res = recognize_gender(full_name, Genderize(country_id="IT"))

        if res != np.nan:
            return res
        else:
            logging.info("\tTrying in all the regions..")
            return recognize_gender(full_name, Genderize())

    return np.nan

from configparser import ConfigParser

class InfoParser:
    info_section = ""
    file_path = ""
    config_object = None
    data_info = dict()

    def __init__(self,file_path,info_section = "DATA_INFO"):
        self.info_section = info_section
        self.file_path = file_path

        # Read ini file
        self.config_object = ConfigParser()
        self.config_object.read(file_path)

        # Get the DATA_INFO section
        self.data_info = self.config_object["DATA_INFO"]

    def info(self,key,value):
        self.data_info[key] = str(value)
        self.save_file()

    def save_file(self):
        with open(self.file_path, 'w') as conf:
            self.config_object.write(conf)

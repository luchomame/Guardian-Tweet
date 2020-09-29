#!/usr/bin/env python
import tweepy as tw
import pandas as pd
import re
from textblob import TextBlob
import mysql.connector
import numpy as np

'''
DON'T TOUCH!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
'''
consumer_key = 'MNv539z4C74XY7Ic4nl8dEpfd'
consumer_secret = 'ZJwNtgVEK3o2yy9tEIWmwbMhVkpmtmZtRK4uz3QtvyQNYIbxLF'
access_token = '1223716040548417539-REnwuuX2f2rvbgaqizIiOdpCpqAM6A'
access_token_secret = 'g2sKZzMYUiqAx5zHPWDX9jLO7S889NhtwaxCF51egpD3e'

auth = tw.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_token, access_token_secret)
api = tw.API(auth,
             wait_on_rate_limit=True)  # can only get a 180 tweets per per 15 mins so wait on makes app wait 15 mins

# states ,(WOULD THEORETICALLY BE IN DB)
states = {
    "Alabama": ["Birmingham"],
    "Alaska": ["Anchorage"],
    "Arizona": ["Phoenix"],
    "Arkansas": ["Little_Rock"],
    "California": ["Los_Angeles"],
    "Colorado": ["Denver"],
    "Connecticut": ["Bridgeport"],
    "Delaware": ["Wilmington"],
    "Florida": ["Jacksonville"],
    "Georgia": ["Atlanta"],
    "Hawaii": ["Honolulu"],
    "Idaho": ["Boise"],
    "Illinois": ["Chicago"],
    "Indiana": ["Indianapolis"],
    "Iowa": ["Des Moines"],
    "Kansas": ["Wichita"],
    "Kentucky": ["Louisville"],
    "Louisiana": ["New Orleans"],
    "Maine": ["Portland"],
    "Maryland": ["Baltimore"],
    "Massachusetts": ["Boston"],
    "Michigan": ["Detroit"],
    "Minnesota": ["Minneapolis"],
    "Mississippi": ["Jackson"],
    "Missouri": ['Kansas City'],
    "Montana": ["Billings"],
    "Nebraska": ["Omaha"],
    "Nevada": ["Las Vegas"],
    "New Hampshire": ["Manchester"],
    "New Jersey": ["Newark"],
    "New Mexico": ["Albuquerque"],
    "New York": ["New York City"],
    "North Carolina": ["Charlotte"],
    "North Dakota": ["Fargo"],
    "Ohio": ["Columbus"],
    "Oklahoma": ["Oklahoma City"],
    "Oregon": ["Portland"],
    "Pennsylvania": ["Philadelphia"],
    "Rhode Island": ["Providence"],
    "South Carolina": ["Charleston"],
    "South Dakota": ["Sioux Falls"],
    "Tennessee": ["Nashville"],
    "Texas": ["Houston"],
    "Utah": ["Salt Lake City"],
    "Vermont": ["Burlington"],
    "Virginia": ["Virginia Beach"],
    "Washington": ["Seattle"],
    "West Virginia": ["Charleston"],
    "Wisconsin": ["Milwaukee"],
    "Wyoming": ["Cheyenne"]
}

# connect DB
mydb = mysql.connector.connect(
    host="localhost",
    user='root',
    passwd='Cc4812850!!',
    database='checkin'
)
mycursor = mydb.cursor()


# ALSO INCLUDE A SEARCH BY HANDLE (list of handles will be in DB)

def clean_tweet(tweet):
    # remove links, special chars, etc.
    return ' '.join(re.sub("([A-Za-z0-9]+)|([^0-9A-Za-z \t]) |(\n://+)", " ", tweet).split())


def get_tweet_sentiment(tweet):
    # classify sentiment of passed tweet using textblob's sentiment method
    analysis = TextBlob(clean_tweet(tweet))
    if analysis.sentiment.polarity > 0:
        return 'positive'
    elif analysis.sentiment.polarity == 0:
        return 'neutral'
    else:
        return 'negative'


def get_tweets(search_words, date_since, count=10):
    # post a tweet from python
    # api.update_status("Look, I'm tweeting from #Pyhton in my #earthanalytics class!")

    # collect tweets
    tweets = tw.Cursor(api.search,
                       q=search_words,
                       lang="en",
                       since=date_since).items(count)

    return tweets

def createTables(name, tweetList):
    #drop table if exists
    #query1 = "DROP table " + name
    #mycursor.execute(query1)
    query = "CREATE TABLE IF NOT EXISTS " + name + " (tweets TEXT);"
    mycursor.execute(query)
    print(name)
    for tweet in tweetList:
        tweet = tweet.replace('\n', ' ')
        tweet = tweet.replace('\t' or 'RT', ' ')
        try:
            query = "INSERT INTO " + name + " VALUES (\'" + tweet + "\');"
            #print(query)
            mycursor.execute(query)
        except:
            print(query + " did not work")

    #print()


def main():
    for i in states.values():
        city = i[0]
        disaster = 'coronavirus'  # should come from disaster file db
        # define the search term
        search = disaster + " " + city
        date = "2019-01-01"
        count = 5
        pCount = 0
        nCount = 0
        tweets = get_tweets(search, date, count)
        # if tweets.page_index == -1 :
        # continue
        tweetList = list()
        for tweet in tweets:
            sentiment = get_tweet_sentiment(tweet.text)
            tweetList.append(tweet.text)
            if sentiment == "positive":
                pCount += 1
            elif sentiment == "negative":
                nCount += 1
        # add negative and positive
        # print(i)
        pNeg = nCount / count * 100
        pPos = pCount / count * 100
        isRisk = "True" if pNeg > pPos else "False"
        i.append(str(pNeg))
        i.append(str(pPos))
        i.append(isRisk)
        # for right now just save pos and neg to state dict
        ####format is state(city, neg, pos)
        createTables(i[0], tweetList)
        '''#create csv
        filename = i[0]+".csv"
        #make pandas df
        df = pd.DataFrame({i[0]: tweetList})
        df.to_csv(filename)'''



'''mycursor = mydb.cursor()
query = "CREATE TABLE " + "Alabama" + "(id INT AUTO_INCREMENT, tweets TEXT, PRIMARY KEY (id))"
mycursor.execute(query)
for x in mycursor:
    print(x)'''

main()

'''
look up total occurence of city names
"praying for dallas" and shit
users_locs = [[tweet.user.screen_name, tweet.user.location] for tweet in tweets]
users_locs


users_locs = [[tweet.text, tweet.user.location] for tweet in tweets]

tweet_text = pd.DataFrame(data=users_locs,
                          columns=['tweet', 'location'])
tweet_text = pd.DataFrame({
                    "location": [tweet.user.location for tweet in tweets],
                    "tweet": [tweet.text for tweet in tweets]
})
'''

"""
Calculate the area of a circular field
"""
import json
import math

def run(input_file):

    # Open input file and load json file
    fp = open(input_file)
    input_data = json.load(fp)
    fp.close()

    # Open output file

    return math.pi*float(input_data["radius"])**2



def check_columns(df,info):
	extra_in_info = list(set(info.index)-set(df.columns))
	extra_in_df   = list(set(df.columns)-set(info.index))
	if len(extra_in_info):
		print(f"There are {len(extra_in_info)} extra columns in info:",end=" ")
		print(extra_in_info)
	else:
		print("No extra columns in info")
	if len(extra_in_df):
		print(f"There are {len(extra_in_df)} extra columns in df:",end=" ")
		print(extra_in_df)
	else:
		print("No extra columns in df")